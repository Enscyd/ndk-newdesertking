-- CreateTable
CREATE TABLE `Trips` (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `companyId` INTEGER NOT NULL,
    `destinationId` INTEGER NOT NULL,
    `employeeId` INTEGER NOT NULL,
    `truckId` INTEGER NOT NULL,
    `tripType` VARCHAR(191) NOT NULL,
    `driverAmount` DOUBLE NULL,
    `tripDate` DATETIME(3) NOT NULL,
    `tripAmount` DOUBLE NOT NULL,
    `isOmani` VARCHAR(191) NOT NULL,
    `omaniName` VARCHAR(191) NULL,
    `omaniAmount` DOUBLE NULL,
    `image` VARCHAR(191) NULL,

    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
