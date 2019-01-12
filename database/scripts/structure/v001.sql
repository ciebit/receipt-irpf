CREATE TABLE `cb_receipt_irpf` (
    `id` INT(5) UNSIGNED NOT NULL AUTO_INCREMENT ,
    `file_id` INT(10) UNSIGNED NOT NULL ,
    `year` YEAR UNSIGNED NOT NULL ,
    `year_calendar` YEAR UNSIGNED NOT NULL ,
    `association_id` VARCHAR(200) NULL ,
    `status` TINYINT(1) UNSIGNED NOT NULL ,
    PRIMARY KEY  (`id`)
) ENGINE = InnoDB COMMENT = 'version:001';
