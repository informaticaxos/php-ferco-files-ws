The project is a small PHP REST-style API (no framework) organized with a simple MVC-like structure.

Quick summary
- Entry point: `index.php` — basic router: it imports route arrays from `src/routes/*`, merges them and dispatches requests to controller methods.
- MVC layers:
  - Controllers: `src/controllers/*Controller.php` — deal with HTTP I/O (json in/out), status codes and call services.
  - Services: `src/services/*Service.php` — business logic, validation, orchestration between repositories and models.
  - Repositories: `src/repositories/*Repository.php` — direct PDO usage, CRUD SQL mapped to model objects.
  - Models: `src/models/*Model.php` — simple data holders with `toArray()` used for JSON responses.
- DB config: `src/config/database.php` — provides a singleton PDO via `Database::getConnection()`.
- Routes: `src/routes/*Route.php` — define `$routes` arrays with keys like `"GET /forms/{id}" => ['FormController','getById']`.

What to know when editing/adding code
- Routing: `index.php` matches route patterns by replacing `{param}` with `([^/]+)` regex groups. Keep route patterns consistent with existing files (use `{id}` for numeric identifiers).
- Controllers must send responses using the controller helper `sendResponse($httpStatus, $status, $message, $data)` to keep response shape consistent: {status, message, data}.
- Services return model objects (or arrays) and avoid echoing or setting headers. Controllers handle HTTP layer concerns.
- Repositories use PDO prepared statements. Use `Database::getConnection()`; expect `findById` to return associative arrays, while `save()` and `delete()` operate on Model objects or IDs.

Conventions and patterns specific to this repo
- File layout: one class per file, autoloading is not present — uses `require_once` relative includes. When adding files, remember to `require_once` where needed (controllers require services, services require repositories and models, etc.).
- No composer/autoloading: add manual `require_once` lines in files that need dependencies or update `index.php` if adding route files.
- Responses: JSON with three top-level keys: `status` (0|1), `message` (string), `data` (object|array|null). Controllers call `exit` after printing json.
- Date validation: services validate dates using `strtotime()` (see `FormService::createForm`/`updateForm`). Follow the same approach unless adding a stricter validator.
- ID columns: Repositories expect primary keys named `id_form` (for forms) — keep SQL column names consistent with existing table schemas.

Important files to reference when working
- `index.php` — request dispatching and route merging
- `src/routes/FormRoute.php`, `src/routes/DocumentsRoute.php` — example route definitions
- `src/controllers/FormController.php`, `src/controllers/DocumentsController.php` — how controllers shape responses
- `src/services/FormService.php`, `src/services/DocumentsService.php` — validation and service patterns
- `src/repositories/FormRepository.php`, `src/repositories/DocumentsRepository.php` — PDO usage and SQL patterns
- `src/config/database.php` — DB credentials (sensitive; do not commit real credentials in public repos)

Developer workflows & quick commands
- Run a quick PHP syntax check on modified files:
  php -l path\to\file.php
- Run the app locally: serve with PHP's built-in server from repository root:
  php -S localhost:8000
  Then call endpoints like `GET http://localhost:8000/forms` (the router strips `/php-ferco-files-ws` if present in REQUEST_URI).

Security and secrets
- `src/config/database.php` currently holds DB credentials. Treat it as sensitive. For local development consider replacing values with environment variables or a separate local config file that is gitignored.

When you need to add features
- Add the route in `src/routes/<Name>Route.php`, then `require_once` that file in `index.php` (or add a new file following existing pattern). Route keys must start with HTTP method followed by space and path.
- Add a Controller that uses a Service. Services should call Repositories. Repositories call `Database::getConnection()`.

Edge cases/limitations discovered
- No input sanitization beyond basic checks in services. Repositories use prepared statements but controllers rely on services for validation.
- No automated tests or static analysis configured.
- No autoloading/composer — manual requires required.

If something is unclear
- Point to the file and line you inspected (example: `src/services/FormService.php` line ~40) and ask whether to follow the same pattern or refactor to a different approach (e.g., add composer/autoloading).

Examples (copy-paste patterns)
- Route entry:
  'PUT /forms/{id}' => ['FormController', 'update']
- Controller response shape:
  $this->sendResponse(200, 1, 'Form updated successfully', $form->toArray());

Keep edits minimal and consistent with existing files: prefer extending current controllers/services rather than large rewrites unless requested.

Ready for review — tell me which part you'd like expanded or if you want automatic conversion to PSR-4 + composer autoloading.
