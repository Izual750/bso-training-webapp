# BSO Training WebApp

A Redis-based IP hit counter web application built with PHP 8.3, featuring a modern UI with a cute ASCII cat mascot and real-time statistics tracking.

## Features

- 🎯 IP-based visit tracking using Redis Sentinel
- 📊 Global statistics (unique IPs, total hits)
- 🖥️ System information display
- 🐱 ASCII art cat mascot (slightly transparent)
- 📱 Responsive design
- ⚡ Modern UI with smooth animations
- 🔄 Automatic Docker image builds on release

## Docker Image

Public Docker images are automatically built and published to GitHub Container Registry on each release.

### Pull the image

```bash
docker pull ghcr.io/izual750/bso-training-webapp:latest
```

### Run with Docker

```bash
docker run -d \
  -p 9000:9000 \
  -e REDIS_HOST=redis-sentinel \
  -e REDIS_PORT=6379 \
  -e REDIS_PASSWORD=your-password \
  ghcr.io/izual750/bso-training-webapp:latest
```

## Local Development with Docker Compose

The easiest way to run the application locally is using Docker Compose.

### Quick Start

1. Clone the repository:
```bash
git clone git@github.com:Izual750/bso-training-webapp.git
cd bso-training-webapp
```

2. Start all services:
```bash
docker-compose up -d
```

3. Open http://localhost:8081 in your browser

4. Stop all services:
```bash
docker-compose down
```

### What's Included

The `docker-compose.yml` file includes:
- **webapp** - PHP built-in server (port 8081)
- **redis** - Redis cache (port 6379, password: `devpassword`)

All services are connected via a bridge network and include health checks.

### Development Mode

The compose file mounts your local files as volumes, so you can edit:
- `main.php`
- `styles.css`
- `script.js`

Changes will be reflected immediately (just refresh your browser).

### View Logs

```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f webapp
docker-compose logs -f redis
```

## Alternative: Local Development (Without Docker)

### Prerequisites

- PHP 8.3+
- Redis server
- PHP Redis extension

### Setup

1. Clone the repository:
```bash
git clone git@github.com:Izual750/bso-training-webapp.git
cd bso-training-webapp
```

2. Set environment variables:
```bash
export REDIS_HOST=localhost
export REDIS_PORT=6379
export REDIS_PASSWORD=your-password  # optional
```

3. Run with PHP built-in server:
```bash
php -S localhost:8000 main.php
```

4. Open http://localhost:8000 in your browser

## Production Docker Compose Example

```yaml
version: '3.8'

services:
  redis:
    image: redis:alpine
    ports:
      - "6379:6379"
    command: redis-server --requirepass yourpassword

  webapp:
    image: ghcr.io/izual750/bso-training-webapp:latest
    ports:
      - "8080:8080"
    environment:
      REDIS_HOST: redis
      REDIS_PORT: 6379
      REDIS_PASSWORD: yourpassword
    depends_on:
      - redis
```

## Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `REDIS_HOST` | Redis Sentinel hostname | `redis-sentinel` |
| `REDIS_PORT` | Redis Sentinel port | `6379` |
| `REDIS_PASSWORD` | Redis password (optional) | `null` |

## Architecture

- **Backend**: PHP 8.3 built-in server with Redis extension
- **Cache/Storage**: Redis Sentinel
- **Frontend**: Vanilla JavaScript with modern CSS
- **Container**: Alpine-based Docker image (~50MB)
- **No nginx required**: PHP built-in server handles both static files and PHP execution

## File Structure

```
.
├── main.php                      # Application controller (business logic)
├── helpers.php                   # Helper functions (utilities)
├── templates/                    # HTML templates (presentation layer)
│   ├── index.template.php        # Main page template
│   └── error.template.php        # Error page template
├── styles.css                    # Stylesheet with ASCII cat
├── script.js                     # Client-side animations
├── Dockerfile                    # Docker image definition
├── docker-compose.yml            # Local development setup
├── .github/
│   └── workflows/
│       └── docker-publish.yml    # CI/CD workflow
├── .gitignore                    # Git ignore rules
└── README.md                     # This file
```

### Architecture Pattern

The application follows a **separation of concerns** pattern:

- **`main.php`** - Controller: Handles Redis logic, data processing, and orchestration
- **`helpers.php`** - Utilities: Reusable functions (IP detection, formatting, template rendering)
- **`templates/*.php`** - Views: Pure HTML presentation with minimal PHP (only for output)

This structure makes the code:
- ✅ Easier to maintain and test
- ✅ Clearer separation between logic and presentation
- ✅ More reusable (helpers can be used anywhere)
- ✅ Better organized for team collaboration

## Creating a Release

To trigger a new Docker image build:

1. Create a new tag:
```bash
git tag -a v1.0.0 -m "Release version 1.0.0"
git push origin v1.0.0
```

2. Create a release on GitHub from the tag

The GitHub Actions workflow will automatically build and push the image to `ghcr.io/izual750/bso-training-webapp` with the following tags:
- `v1.0.0` (exact version)
- `v1.0` (minor version)
- `v1` (major version)
- `latest`

## Security

✅ **No hardcoded credentials** - All sensitive data is passed via environment variables:
- Redis host, port, and password are configurable
- No secrets in the codebase
- Safe for public repositories

## How It Works

1. Each visitor's IP address is tracked
2. Redis stores a counter for each unique IP
3. Counters expire after 24 hours
4. The page displays:
   - Your IP address
   - Your visit count with ordinal suffix (1st, 2nd, 3rd, etc.)
   - Total unique IPs
   - Total hits across all IPs
   - System information (PHP version, hostname, server software, etc.)

## UI Features

- **Gradient background** with purple/blue theme
- **ASCII cat mascot** centered behind content (15% opacity)
- **Hover effects** on cards and stats
- **Smooth animations** on page load
- **Responsive design** for mobile devices
- **Modern card layout** with shadows and gradients

## License

This is a public project - feel free to clone and use!

## Contributing

Contributions are welcome! Feel free to open issues or submit pull requests.

## Support

For issues or questions, please open an issue on GitHub.
