-- CreateTable
CREATE TABLE `Expenses` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `employeeId` INTEGER NOT NULL,
    `truckId` INTEGER NOT NULL,
    `expenseDate` DATETIME(3) NOT NULL,
    `category` VARCHAR(191) NOT NULL,
    `details` VARCHAR(191) NULL,
    `amount` DOUBLE NOT NULL,
    `image` VARCHAR(191) NULL,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
