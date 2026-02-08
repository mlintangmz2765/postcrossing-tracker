# User Guide

## Getting Started

### Logging In
1. Navigate to your Postcard Tracker URL
2. Enter your username and password
3. Complete the ReCaptcha verification
4. Click "Login"

---

## Dashboard

The dashboard shows an overview of your postcard collection:

- **Total Sent** - Number of postcards you've sent
- **Total Received** - Number of postcards you've received
- **Countries Reached** - Unique countries in your collection
- **Traveling** - Postcards that haven't arrived yet
- **Recent Notifications** - New arrival confirmations

---

## Registering a New Postcard

### For Sent Postcards
1. Go to **"New Postcard"** from the navigation
2. Select **"Sent"** tab
3. Fill in the details:
   - **Postcard ID** (e.g., ID-447230) - optional
   - **Send Date** - when you mailed it
   - **Recipient Name** - who you're sending to
   - **Address & Country** - destination address
   - **Description** - what's on the postcard
   - **Stamp Cost** - postage amount
4. Upload photos of the front and back (optional)
5. Add stamp photos (optional)
6. Click **"Register"**

### For Received Postcards
1. Go to **"New Postcard"**
2. Select **"Received"** tab
3. Fill in sender details (this will create or update a **Unified Contact**)
4. Upload postcard and stamp photos
5. Click **"Register"**

---

## Bulk Import from CSV

For migrating existing postcard data, use the **Import** feature:

1. Go to **"/import"** from the navigation
2. Prepare a CSV file with semicolon (`;`) separator
3. Required columns: `type`, `postcard_id`, `tgl_kirim`, `tgl_terima`, `deskripsi`, `nama`, `alamat`, `negara`, `telepon`, `biaya_asal`, `mata_uang`
4. Upload the file and review the preview
5. Click **"Import"**

> ⚠️ **Note:** Date format must be DD/MM/YYYY

---

## Unified Contact System

The application uses a **Unified Contact System** to manage PII efficiently and accurately:
- **Address Book**: When you enter a recipient/sender name, the system suggests previous contacts.
- **Centralized PII**: Address, phone number, and coordinates are stored centrally in the `contacts` table.
- **Automatic Sync**: If you change the address while registering a new postcard for the same contact, the contact will be updated automatically (if the save option is selected).
- **Coordinates**: Map locations are now associated directly with the contact, not individual postcards.

---

## QR Code for Arrival Confirmation

On the **View Postcard** page for **sent** postcards, you can download a **QR Code**:

1. Open any sent postcard from your dashboard
2. Look for the **QR Code** section
3. Click **"Download QR"** to save the image
4. Print and **attach the QR code sticker to your postcard** before mailing
5. When the recipient scans the QR code with their phone, they'll be taken directly to the arrival confirmation page
6. Once confirmed, you'll receive an email notification

---

## Viewing Statistics

The **Statistics** page shows:
- Postcards by country (pie chart)
- Send/receive trends over time
- Average delivery times
- Total stamp costs
- Distance traveled

---

## Galleries

### Private Gallery
View all your postcards in a visual grid. Click any card to view details.

### Public Gallery
Share your collection publicly at `/gallery`. This page doesn't require login.

### Stamp Gallery
Browse your stamp collection separately.

---

## Editing Postcards

1. Click on any postcard to view details
2. Click **"Edit"** button
3. Modify any fields
4. Add or remove stamp photos
5. Click **"Save Changes"**

---

## Deleting Postcards

1. Open the postcard in edit mode
2. Scroll down and click **"Delete"**
3. Confirm the deletion

⚠️ **Warning:** Deleted postcards cannot be recovered!

---

## Tips

- Use consistent country names for better statistics
- Upload high-quality photos for the gallery
- Fill in stamp costs to track your postage spending
- Check notifications regularly for arrival confirmations
