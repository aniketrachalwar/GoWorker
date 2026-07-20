# GoWorker Developer Contribution Guidelines

Welcome to the team! Since we have **6 developers** actively working on this codebase, staying organized is critical to avoid merge conflicts and database discrepancies. Please read and adhere to these guidelines before starting your tasks.

---

## 🌳 Git Development Workflow

We follow the **Feature Branching Model** to ensure our `main` branch remains clean and deployable.

### 1. Branch Naming Rules
Always create a branch from the latest `main` branch before starting work. Use standard prefixes:
* `feature/<short-description>`: Adding new pages, tables, features (e.g. `feature/worker-rating`)
* `bugfix/<short-description>`: Resolving existing bugs (e.g. `bugfix/login-csrf-bypass`)
* `refactor/<short-description>`: Reworking structure without adding new features (e.g. `refactor/navbar-styles`)

*Example:* `git checkout -b feature/booking-notifications`

### 2. Synchronization Checklist
Before pushing your commits and creating a Pull Request (PR):
1. **Pull Latest Main**: Incorporate changes from other developers first.
   ```bash
   git checkout main
   git pull origin main
   git checkout your-feature-branch
   git merge main
   ```
2. **Resolve Conflicts**: Address code conflicts locally in your editor. Ensure the application compiles and runs locally on your XAMPP server after the merge.

---

## 🗄️ Database Change Protocol

Because this is a vanilla PHP & SQL project and we don't have an automated framework migration system, **do not edit the main schema file `goworker.sql` directly** unless it is a fresh base install change. 

If your feature requires a database change (such as adding a table, adding a column, or modifying constraint types):

1. **Create a Patch File**: 
   Inside the `database/` directory, create a new patch file named chronologically:
   * Format: `patch_YYYYMMDD_feature_name.sql` (e.g., `database/patch_20260720_add_user_avatars.sql`)
2. **Add standard SQL statements**:
   ```sql
   USE `goworker`;
   ALTER TABLE `users` ADD COLUMN `avatar_url` VARCHAR(255) DEFAULT NULL;
   ```
3. **Commit the Patch File**: Push the patch file with your code. Other developers can then run that patch file locally in phpMyAdmin or command line to keep their databases synced without wiping out test data.

---

## 📝 Code Standards & Cleanliness

### 1. PHP Style Guide
* **Security First**: 
  * Always use PDO prepared statements with parameter binding for querying databases. Do not concatenate variables directly inside SQL statements.
  * Sanitize all user inputs using HTML escaping helper `e()` before printing them onto the page.
* **CSRF Protection**: Form submissions must include `<?php echo csrf_field(); ?>` and be validated on the server using `verify_csrf_token()`.
* **Clean Code**: Remove debug statements (`var_dump()`, `print_r()`) and comments that are no longer relevant before committing.

### 2. Styling Rules
* Use class names and ids defined in the global stylesheet where possible, rather than inline CSS.
* Keep styling responsive. Test pages in responsive/mobile layouts.

---

## 🔍 Pull Request & Code Review Process

1. **Push & Open a Pull Request**: Push your local branch to GitHub and create a PR targeting `main`.
2. **Assign Reviewers**: Set at least one team member as a reviewer. The **Team Leader** has final merge approval.
3. **Write a Description**: Provide a brief description of what your PR changes and attach a screenshot if it changes user interfaces.
4. **Approval Requirement**: No PR may be merged to `main` without passing manual verification and receiving at least one approval.
