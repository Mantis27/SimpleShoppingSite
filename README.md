# Simple Shopping Site
This is a project by HO Tsz Ngong, improved based on simple project previously done in IERG4210.

To show that Ajax pagination is being implemented, the current setting is 2 items per pages.\
Please use sandbox Paypal account.

Two users available for now:
- admin@mantis.com, dj007
- normal1@mantis.com, normal1

The directory structure would be as following:
```bash
/var/www/html/
├── Catagory
│   ├── index.php
│   └── style_cat.css
├── Items
│   ├── index.php
│   └── style_item.css
├── Resources
│   └── Item_Photo
│       └── (id).img
├── lib
│   ├── add_prod.js
│   ├── admin-script.js
│   ├── auth.php
│   ├── checkout_items.php
│   ├── db.inc.php
│   ├── nonce.php
│   ├── show_items.php
│   ├── show_orders.php
│   ├── show_prod.js
│   ├── store_items.php
│   ├── upload_order.php
│   └── user_portal_script.js
├── admin-process.php
├── admin.php
├── auth-process.php
├── changepw.php
├── checkoutpage.php
├── index.php
├── listener.php
├── login.php
├── logout.php
├── payment-success.html
├── style1.css
├── stylefortable.css
└── userportal.php
```

cart.db should be in /var/www/
