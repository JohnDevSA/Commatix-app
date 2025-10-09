---
description: Create a new Filament resource with full CRUD
argument-hint: "<ModelName>"
---

Create a complete Filament resource for the specified model in the Commatix multi-tenant application.

**Requirements:**
- Model name must be provided as an argument
- Follow existing Commatix patterns for multi-tenancy
- Include proper authorization and scoping
- Add tenant awareness where applicable

**Steps:**
1. Check if the model exists in `app/Models/`
2. Generate Filament resource: `php artisan make:filament-resource {ModelName} --generate`
3. Update the resource to follow Commatix patterns:
   - Add tenant scoping if needed
   - Implement proper authorization
   - Add relationship managers if applicable
   - Follow the existing navigation structure
4. Add to appropriate navigation group in AppPanelProvider
5. Test the resource in browser if possible

**Example Models in Commatix:**
- Tenant, Division, ApprovalGroup
- Task, Milestone, WorkflowTemplate
- Subscriber, SubscriberList
- User, UserType, DocumentType
