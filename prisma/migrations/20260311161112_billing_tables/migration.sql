-- CreateTable
CREATE TABLE `Billings` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `invoiceNo` VARCHAR(191) NOT NULL,
    `companyId` INTEGER NOT NULL,
    `date` DATETIME(3) NOT NULL,
    `billImage` VARCHAR(191) NULL,
    `totalAmount` DOUBLE NULL,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updatedAt` DATETIME(3) NOT NULL,

    UNIQUE INDEX `Billings_invoiceNo_key`(`invoiceNo`),
    INDEX `Billings_companyId_idx`(`companyId`),
    INDEX `Billings_date_idx`(`date`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `BillingItems` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `billingId` INTEGER NOT NULL,
    `description` VARCHAR(191) NOT NULL,
    `vehicleNo` VARCHAR(191) NOT NULL,
    `quantity` DOUBLE NOT NULL,
    `rent` DOUBLE NOT NULL,
    `taxableAmount` DOUBLE NOT NULL,
    `vat` DOUBLE NOT NULL,
    `totalAmount` DOUBLE NOT NULL,

    INDEX `BillingItems_billingId_idx`(`billingId`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- AddForeignKey
ALTER TABLE `BillingItems` ADD CONSTRAINT `BillingItems_billingId_fkey` FOREIGN KEY (`billingId`) REFERENCES `Billings`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
