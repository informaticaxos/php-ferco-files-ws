# TODO List for PHP API-REST Project

## Task: Create a basic model for the table with fields: id, titulo, descripcion, tipo, path

- [x] Create src/models/Archivo.php with the model class including properties (id, titulo, descripcion, tipo, path), constructor, getters, and setters.
- [x] Verify the model structure.

## Additional Task: Create repository layer

- [x] Create src/config/database.php for database connection setup.
- [x] Create src/repository/ArchivoRepository.php with CRUD methods (findAll, findById, save, update, delete).

## Additional Task: Create services, controllers, and routes

- [x] Create src/services/ArchivoService.php with business logic methods.
- [x] Create src/controllers/ArchivoController.php with methods for handling HTTP requests.
- [x] Create src/routes/archivoRoutes.php to define the REST routes.
- [x] Create index.php as the entry point for the application.

## New Task: Adapt all layers to the 'forms' table

- [ ] Rename and update src/models/Archivo.php to Form.php with fields id_form, name, date, status.
- [ ] Rename and update src/repository/ArchivoRepository.php to FormRepository.php for table 'forms'.
- [ ] Rename and update src/services/ArchivoService.php to FormService.php.
- [ ] Rename and update src/controllers/ArchivoController.php to FormController.php.
- [ ] Rename and update src/routes/archivoRoutes.php to formRoutes.php with /forms routes.
- [ ] Update index.php to use the new Form classes and routes.
