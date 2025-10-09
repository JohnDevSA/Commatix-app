---
description: Review code for SOLID principles compliance
argument-hint: "[file-path]"
---

Review code in Commatix for SOLID principles compliance and suggest improvements.

**Focus Areas:**

1. **Single Responsibility Principle (SRP)**
   - Each class should have one reason to change
   - Check for god classes or classes doing too much

2. **Open/Closed Principle (OCP)**
   - Classes should be open for extension, closed for modification
   - Look for proper use of interfaces and polymorphism

3. **Liskov Substitution Principle (LSP)**
   - Derived classes should be substitutable for base classes
   - Check inheritance hierarchies

4. **Interface Segregation Principle (ISP)**
   - Many client-specific interfaces are better than one general-purpose interface
   - Review existing interfaces in `app/Contracts/Services/`

5. **Dependency Inversion Principle (DIP)**
   - Depend on abstractions, not concretions
   - Check for proper dependency injection

**Process:**
1. If file-path provided, review that specific file
2. Otherwise, review recently changed files or ask which area to review
3. Check against existing patterns in:
   - `app/Contracts/Services/` (interfaces)
   - `app/Services/` (service implementations)
4. Provide specific, actionable recommendations
5. Reference existing good examples from the codebase

**Commatix-Specific Patterns:**
- Repository pattern for data access
- Service classes for business logic
- Interface-first design
- Dependency injection through Laravel's container
