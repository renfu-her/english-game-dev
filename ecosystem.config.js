export default {
  apps: [
    {
      name: 'websocket-server',
      script: 'websocket-server.js',
      watch: true,
      ignore_watch: ['node_modules', 'logs', '*.log'],
      instances: 1,
      autorestart: true,
      max_memory_restart: '1G',
      env: {
        NODE_ENV: 'development',
        PORT: 8888
      },
      env_production: {
        NODE_ENV: 'production',
        PORT: 8888
      },
      log_file: './logs/websocket-server.log',
      out_file: './logs/websocket-server-out.log',
      error_file: './logs/websocket-server-error.log',
      log_date_format: 'YYYY-MM-DD HH:mm:ss Z',
      merge_logs: true,
      time: true
    }
  ]
}; 