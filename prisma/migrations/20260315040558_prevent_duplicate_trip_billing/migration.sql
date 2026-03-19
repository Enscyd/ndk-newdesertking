/*
  Warnings:

  - Added the required column `tripId` to the `BillingItems` table without a default value. This is not possible if the table is not empty.

*/
-- AlterTable
ALTER TABLE `billingitems` ADD COLUMN `tripId` INTEGER NOT NULL;

-- CreateIndex
CREATE INDEX `BillingItems_tripId_idx` ON `BillingItems`(`tripId`);
