# Contributing to Postcrossing Tracker

Thank you for your interest in contributing! This is a personal hobby project, but contributions are welcome.

## How to Contribute

### Reporting Bugs
1. Check if the issue already exists in [GitHub Issues](../../issues)
2. If not, create a new issue with:
   - Clear description of the bug
   - Steps to reproduce
   - Expected vs actual behavior
   - Screenshots if applicable

### Suggesting Features
Open an issue with the `enhancement` label describing:
- The feature you'd like to see
- Why it would be useful
- Any implementation ideas

### Submitting Code

1. **Fork** the repository
2. **Clone** your fork locally
3. **Create a branch** for your changes:
   ```bash
   git checkout -b feature/your-feature-name
   ```
4. **Make your changes** following the coding style
5. **Test** your changes locally
6. **Commit** with clear messages:
   ```bash
   git commit -m "Add: brief description of change"
   ```
7. **Push** to your fork and open a **Pull Request**

### Coding Style
- Follow PSR-12 for PHP code
- Use meaningful variable and function names
- Comment complex logic
- Keep files organized in their respective directories

### Commit Message Prefixes
- `Add:` New feature
- `Fix:` Bug fix
- `Update:` Improvements to existing feature
- `Docs:` Documentation changes
- `Refactor:` Code restructuring
- `Chore:` Maintenance tasks

## Local Development

```bash
# Install dependencies
composer install
npm install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate

# Start development server
php artisan serve
```

## Questions?

Feel free to open an issue or contact the maintainer.

Thank you for contributing! 🙏
