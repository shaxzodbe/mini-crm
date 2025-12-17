# Architectural Decisions Log

## 1. Domain-Driven Design Lite (Services & Repositories)
**Decision**: Split logic into `Controllers`, `Services`, and `Repositories`.
**Reasoning**: 
- **Repositories** (`TicketRepository`) handle data access and complex queries (e.g., filtering, statistics). This keeps Eloquent querying logic isolated.
- **Services** (`TicketService`, `CustomerService`) handle business logic (transactions, file uploads, customer resolution). This adheres to separation of concerns and makes logic reusable (e.g., if we add a CLI interface later).
- **Controllers** remain thin, only handling HTTP requests/responses and delegation.

## 2. Widget Implementation (Standalone Blade + AJAX)
**Decision**: Use a dedicated `WidgetController` and a standalone Blade view with inline styles/scripts or minimal external dependencies.
**Reasoning**: 
- The widget needs to be embeddable via `iframe` on third-party sites.
- Using a full frontend framework (Vue/React) would add build complexity for a simple form.
- Vanilla JS + CSS ensures the widget is lightweight and works without conflict.

## 3. Storage & Files
**Decision**: Use `spatie/laravel-medialibrary`.
**Reasoning**: 
- Standardizes file attachment handling (polymorphic relations).
- simplifies file validations and retrieval (download links).
- Requested explicitly by the user.

## 4. Rate Limiting
**Decision**: Implemented in `StoreTicketRequest` using Repository check (`getRecentTicketByContact`).
**Reasoning**: 
- Validating business rules in FormRequest keeps the Controller clean.
- Checking DB for recent tickets is more robust than simple API throttle (which is IP-based) for this specific requirement (1 per day per phone/email).

## 5. API Design
**Decision**: Use `JsonResource` for consistent responses.
**Reasoning**: 
- Transformations layer ensures the API contract remains stable even if DB schema changes.
- `TicketStatisticsResource` separates statistics presentation from calculation.
