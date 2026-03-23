-- AlterTable
ALTER TABLE `billings` ADD COLUMN `paymentStatus` ENUM('PAID', 'UNPAID') NOT NULL DEFAULT 'UNPAID';
