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
3. Fill in sender details and receive date
4. Upload postcard and stamp photos
5. Click **"Register"**

---

## Contact Auto-Fill

When entering a recipient name, the system will suggest previous contacts. Select one to auto-fill the address and country.

To add a new contact or update an existing one, check the **"Save/Update Contact"** option before submitting.

---

## Sharing Arrival Confirmation Link

For each sent postcard, a unique confirmation link is generated:
```
https://yoursite.com/receive/{uid}
```

Share this link with your recipient. When they visit and confirm arrival, you'll receive an email notification.

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
