INSERT INTO `cb_files`
(`id`, `name`, `description`, `uri`, `extension`, `size`, `views`, `mimetype`, `date_hour`, `metadata`, `status`)
VALUES
('1', 'Title File 1', 'Descrição 1', 'title-file-1.pdf', 'pdf', '10', '0', 'appliation/pdf', '2019-01-12 18:57:00', NULL, '3'),
('2', 'Title File 2', 'Descrição 2', 'title-file-2.pdf', 'pdf', '20', '0', 'appliation/pdf', '2019-01-12 18:58:00', NULL, '3'),
('3', 'Title File 3', 'Descrição 3', 'title-file-3.pdf', 'pdf', '30', '0', 'appliation/pdf', '2019-01-12 18:59:00', NULL, '3');

INSERT INTO `cb_receipt_irpf`
(`id`, `file_id`, `year`, `year_calendar`, `association_id`, `status`)
VALUES
('1', '1', '2016', '2015', '1', '3'),
('2', '2', '2017', '2016', NULL, '3'),
('3', '3', '2018', '2017', '3', '5');
