# Mini-CRM

Mini-CRM system for handling customer tickets via a widget.

## Requirements
- PHP 8.4
- Laravel 12
- Docker & Docker Compose

## Quick Start (Docker)

1. **Clone and Setup**
   ```bash
   git clone <repo>
   cd mini-crm
   cp .env.example .env
   ```

2. **Start Docker**
   ```bash
   docker-compose up -d --build
   ```

3. **Install Dependencies & Migrate**
   ```bash
   docker-compose exec app composer install
   docker-compose exec app php artisan key:generate
   docker-compose exec app php artisan storage:link
   docker-compose exec app php artisan migrate --seed
   ```

4. **Access**
   - **Widget**: [http://localhost/widget](http://localhost/widget)
   - **Admin Panel**: [http://localhost/manager](http://localhost/manager)
     - **Login**: `manager@crm.test`
     - **Password**: `password`
   - **API Docs**: [http://localhost/docs](http://localhost/docs)

## Widget Embedding
To embed the widget on any website, use the following iframe code:
```html
<iframe src="http://your-domain.com/widget" width="100%" height="600" frameborder="0"></iframe>
```

## API Documentation
Swagger documentation is available at `/docs`.
- `POST /api/v1/tickets`: Create a new ticket.
- `GET /api/v1/ticket-statistics`: Get ticket stats.

## Testing
Run tests inside the container:
```bash
docker-compose exec app php artisan test
```

## Architecture
See `ARCH_DECISIONS.md` for details on design choices.
