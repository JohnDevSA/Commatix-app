---
description: Generate code from Blueprint YAML
argument-hint: "[draft-file]"
---

Generate Laravel code from Blueprint YAML definition.

**Process:**
1. Check if draft.yaml exists or use provided file
2. Show the draft file contents
3. Run Blueprint generation: `php artisan blueprint:build`
4. Show what was generated:
   - Models
   - Migrations
   - Factories
   - Seeders
   - Controllers
   - Tests
5. Run migrations if new ones were created
6. Suggest next steps:
   - Create Filament resources for new models
   - Update existing resources if models changed
   - Run tests
   - Update documentation

**Blueprint features to use:**
- Model definitions with relationships
- Migration definitions
- Controller scaffolding
- Factory definitions for testing
- Seeder generation

**Example Draft:**
Check `draft.yaml` in the project root for the current schema.

**After generation:**
- Review generated code
- Customize for multi-tenancy if needed
- Add to version control
- Update team documentation
