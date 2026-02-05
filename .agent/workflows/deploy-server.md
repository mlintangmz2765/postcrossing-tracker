---
description: How to sync the latest updates and clean up the server
---

// turbo-all
Follow these steps to update your production server and remove the installation files:

1. **Pull the latest code from GitHub**
   ```bash
   git pull origin main
   ```

2. **Force update the tags (to get the updated v1.0.0)**
   ```bash
   git fetch --tags -f
   ```

3. **Switch to the tag (Optional)**
   ```bash
   git checkout v1.0.0
   ```

4. **Delete the setup folder (Security measure)**
   *   **Linux/macOS:**
       ```bash
       rm -rf setup
       ```
   *   **Windows:**
       ```bash
       rmdir /s /q setup
       ```

5. **Clear Laravel Cache (Recommended)**
   ```bash
   php artisan optimize:clear
   ```
