# 🧹 Magento 2 Delete Unused Product Images Module – CloudCommerce_DeleteUnusedImages

## 📌 Overview
The **CloudCommerce_DeleteUnusedImages** Magento 2 module automatically **detects and deletes unused product images** from the `pub/media/catalog/product` folder.  
This ensures your store stays clean, optimized, and fast by removing orphaned media files that are no longer linked to products in the database.

## 🚀 Features
- ✅ Automatically removes **unused product images** from media folder and database  
- ✅ **Command-line (CLI) support** to trigger cleanup manually  
- ✅ **Cron job** runs every day at midnight, cleaning images in **batches of 500**  
- ✅ **Enable/Disable** option from Magento Admin configuration  
- ✅ Works with Magento 2.4.x and PHP 8.x  
- ✅ Optimized for **performance and server storage savings**  

## 🔧 Installation Guide

### 1️⃣ Install via GitHub
Clone this repository into `app/code/`:
\`\`\`bash
cd app/code
git clone https://github.com/your-username/DeleteUnusedImages.git CloudCommerce/DeleteUnusedImages
\`\`\`

### 2️⃣ Enable the module
\`\`\`bash
bin/magento module:enable CloudCommerce_DeleteUnusedImages
bin/magento setup:upgrade
bin/magento cache:flush
\`\`\`

## ⚙️ Configuration (Enable/Disable Module)
Go to:
\`\`\`
Stores → Configuration → General → Delete Unused Images
\`\`\`
- **Enable Module** → Yes / No

## 💻 Usage

### ✅ 1. Run Cleanup Manually via CLI
\`\`\`bash
bin/magento catalog:images:clean-unused
\`\`\`
- Deletes **up to 500 unused images** per execution.
- If the module is disabled in configuration, it will skip execution.

### ✅ 2. Automated Cleanup via Cron
- Runs daily at **midnight (00:00)**.
- Deletes images in **batches of 500**.
- Respects the Enable/Disable configuration.


## 🧪 Testing

### ✅ Unit Tests
Includes PHPUnit tests to validate cleaner logic.

### ✅ Integration Tests
To run integration tests in Magento sandbox:
\`\`\`bash
vendor/bin/phpunit -c dev/tests/integration/phpunit.xml app/code/CloudCommerce/DeleteUnusedImages/Test/Integration/UnusedImagesCleanerTest.php
\`\`\`

## 🏆 Benefits of Using This Module
- 🗑️ Keeps server storage clean by removing unnecessary images  
- ⚡ Improves site performance and reduces backup size  
- 🔒 Safe deletion with database verification  
- 🛠️ Easy to manage with admin configuration  

## 🔗 SEO Keywords
**Magento 2 delete unused images**, **Magento 2 remove orphaned media files**, **Magento 2 clean media folder**, **Magento 2 optimize storage**, **Magento 2 product image cleanup module**

## ✅ Compatibility
- **Magento**: 2.4.x  
- **PHP**: 8.1, 8.2, 8.3, 8.4