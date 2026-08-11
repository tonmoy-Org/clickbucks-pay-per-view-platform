const { Client } = require('ssh2');

const conn = new Client();

console.log('Connecting to VPS...');
conn.on('ready', () => {
  console.log('SSH connection established successfully!');
  
  // 1. Pop the stash on PressFly first to restore their files
  const restorePressFly = 'cd /var/www/monetizearticle-repo/PressFly && git stash pop || true';
  
  conn.exec(restorePressFly, (err, stream) => {
    if (err) throw err;
    
    stream.on('close', () => {
      console.log('Restored PressFly stash.');
      
      // 2. Find directories matching *clickbucks*
      const findCmd = 'find /var/www /home /root -type d -name "*clickbucks*" 2>/dev/null';
      
      conn.exec(findCmd, (err2, stream2) => {
        if (err2) throw err2;
        
        let pathOutput = '';
        stream2.on('data', (data) => {
          pathOutput += data;
        });
        
        stream2.on('close', (code, signal) => {
          const paths = pathOutput.trim().split('\n').filter(Boolean);
          if (paths.length === 0) {
            console.error('Could not find clickbucks project directory on the VPS!');
            conn.end();
            return;
          }
          
          // Select the first path (typically /var/www/clickbucks-pay-per-view-platform or similar)
          const gitRoot = paths[0].trim();
          console.log('Found ClickBucks Git Root at:', gitRoot);
          
          // In the repository, Files/application contains the laravel project
          const appPath = `${gitRoot}/Files/application`;
          console.log('Laravel App Path:', appPath);
          
          // Run deploy commands
          const deployCmd = `
            cd "${gitRoot}" && \
            git stash && \
            git pull origin main && \
            cd "${appPath}" && \
            php update_vps_db.php && \
            rm -f update_vps_db.php && \
            php artisan optimize:clear
          `;
          
          console.log('Running deployment commands:\n', deployCmd);
          
          conn.exec(deployCmd, (err3, stream3) => {
            if (err3) throw err3;
            
            stream3.on('data', (data) => {
              process.stdout.write(data);
            });
            
            stream3.stderr.on('data', (data) => {
              process.stderr.write(data);
            });
            
            stream3.on('close', (code3, signal3) => {
              console.log(`Deployment script finished with exit code ${code3}`);
              conn.end();
            });
          });
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
