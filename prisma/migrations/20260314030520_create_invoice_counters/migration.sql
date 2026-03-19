/*
  Warnings:

  - You are about to drop the column `totalAmount` on the `billings` table. All the data in the column will be lost.
  - Added the required column `updatedAt` to the `BillingItems` table without a default value. This is not possible if the table is not empty.
  - Added the required column `grandTotal` to the `Billings` table without a default value. This is not possible if the table is not empty.

*/
-- DropIndex
DROP INDEX `Billings_date_idx` ON `billings`;

-- AlterTable
ALTER TABLE `billingitems` ADD COLUMN `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    ADD COLUMN `updatedAt` DATETIME(3) NOT NULL;

-- AlterTable
ALTER TABLE `billings` DROP COLUMN `totalAmount`,
    ADD COLUMN `grandTotal` DOUBLE NOT NULL,
    MODIFY `date` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3);

-- CreateTable
CREATE TABLE `invoice_counters` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `year` INTEGER NOT NULL,
    `last_number` INTEGER NOT NULL DEFAULT 0,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updatedAt` DATETIME(3) NOT NULL,

    UNIQUE INDEX `invoice_counters_year_key`(`year`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
