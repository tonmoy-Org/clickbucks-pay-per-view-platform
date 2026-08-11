const { Client } = require('ssh2');

const conn = new Client();

const GITHUB_REPO = 'https://github.com/tonmoy-Org/clickbucks-pay-per-view-platform.git';

console.log('Connecting to VPS...');
conn.on('ready', () => {
  console.log('SSH connection established!');

  // Check all possible deploy locations
  conn.exec('ls /var/www/ppvbucks.com/ 2>/dev/null && echo "---PPV_EXISTS---" || echo "---PPV_MISSING---"', (err, stream) => {
    if (err) throw err;
    let out = '';
    stream.on('data', d => out += d);
    stream.stderr.on('data', d => process.stderr.write(d));
    stream.on('close', () => {
      console.log('ppvbucks.com contents:', out.trim());

      // Check if it has a Laravel app already
      conn.exec('ls /var/www/ppvbucks.com/artisan 2>/dev/null && echo "LARAVEL" || ls /var/www/ppvbucks.com/Files/application/artisan 2>/dev/null && echo "LARAVEL_REPO" || echo "UNKNOWN"', (err2, stream2) => {
        if (err2) throw err2;
        let out2 = '';
        stream2.on('data', d => out2 += d);
        stream2.stderr.on('data', d => process.stderr.write(d));
        stream2.on('close', () => {
          console.log('Structure check:', out2.trim());

          // Determine app path
          let appPath;
          if (out2.includes('LARAVEL_REPO')) {
            appPath = '/var/www/ppvbucks.com/Files/application';
          } else if (out2.includes('LARAVEL')) {
            appPath = '/var/www/ppvbucks.com';
          } else {
            // Fresh deploy - clone directly into ppvbucks.com
            appPath = '/var/www/ppvbucks.com';
            console.log('No existing app found, will clone fresh');
          }

          console.log('Using app path:', appPath);
          runDeploy(appPath);
        });
      });
    });
  });

  function runDeploy(appPath) {
    // Detect if this is a git repo
    const deployCmd = `
      if [ -f "${appPath}/artisan" ]; then
        echo "Laravel app found at ${appPath}"
        if [ -d "${appPath}/../.git" ] || [ -d "${appPath}/.git" ]; then
          GITDIR=$([ -d "${appPath}/../.git" ] && echo "${appPath}/.." || echo "${appPath}")
          cd "$GITDIR" && git fetch origin main && git reset --hard origin/main && echo "Git pull done"
        else
          echo "No git repo found, skipping pull"
        fi
        cd "${appPath}"
        php update_vps_db.php 2>&1 || echo "DB script not found, skipping"
        rm -f update_vps_db.php
        php artisan optimize:clear
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
        chmod -R 775 storage bootstrap/cache
        echo "=== DEPLOY COMPLETE ==="
      else
        echo "Fresh clone into ${appPath}..."
        rm -rf "${appPath}"
        git clone ${GITHUB_REPO} /tmp/clickbucks_clone
        cp -r /tmp/clickbucks_clone/Files/application/. "${appPath}/"
        rm -rf /tmp/clickbucks_clone
        cd "${appPath}"
        composer install --no-dev --optimize-autoloader --no-interaction 2>&1
        cp -n .env.example .env 2>/dev/null || true
        chmod -R 775 storage bootstrap/cache
        echo "=== FRESH DEPLOY COMPLETE ==="
      fi
    `;

    console.log('\nRunning deploy...\n');

    conn.exec(deployCmd, (err2, stream2) => {
      if (err2) throw err2;

      stream2.on('data', d => process.stdout.write(d));
      stream2.stderr.on('data', d => process.stderr.write(d));

      stream2.on('close', (code) => {
        console.log(`\nDeploy finished with exit code: ${code}`);
        console.log('\n=== ALL DONE ===');
        conn.end();
      });
    });
  }

}).connect({
  host: '145.223.22.69',
  port: 22,
  username: 'root',
  password: `Ti13xphE'5ZU&7Vx`,
  readyTimeout: 20000,
});
