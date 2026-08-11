const { Client } = require('ssh2');

const conn = new Client();

console.log('Connecting to VPS...');
conn.on('ready', () => {
  console.log('SSH connection established successfully!');
  
  // 1. Restore PressFly stash just in case it was left stashed
  const restorePressFly = 'cd /var/www/monetizearticle-repo/PressFly && git stash pop || true';
  
  conn.exec(restorePressFly, (err, stream) => {
    if (err) throw err;
    stream.on('close', () => {
      console.log('PressFly stash restored/checked.');
      
      // 2. Deploy to ppvbucks.com
      const deployCmd = `
        cd "/var/www/ppvbucks.com" && \
        git stash && \
        git pull origin main && \
        cd "Files/application" && \
        php update_vps_db.php && \
        rm -f update_vps_db.php && \
        php artisan optimize:clear
      `;
      
      console.log('Running deploy on ppvbucks.com...');
      conn.exec(deployCmd, (err2, stream2) => {
        if (err2) throw err2;
        
        stream2.on('data', (data) => {
          process.stdout.write(data);
        });
        
        stream2.stderr.on('data', (data) => {
          process.stderr.write(data);
        });
        
        stream2.on('close', (code, signal) => {
          console.log(`\nDeployment finished with code ${code}`);
          conn.end();
        });
      });
    });
  });
}).connect({
  host: '145.223.22.69',
  port: 22,
  username: 'root',
  password: `Ti13xphE'5ZU&7Vx`
});
