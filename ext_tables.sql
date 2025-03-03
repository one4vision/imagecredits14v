#
# Table structure for table 'sys_file_metadata'
#
CREATE TABLE sys_file_metadata (
	tx_imagecredits14v_name tinytext,
	tx_imagecredits14v_link tinytext,
	tx_imagecredits14v_exlist tinyint(4) unsigned NOT NULL DEFAULT '0',
    tx_imagecredits14v_term int(11) unsigned NOT NULL DEFAULT '0',
    keywords text
);

CREATE TABLE tx_imagecredits14v_domain_model_licences (
    name varchar(255) DEFAULT '' NOT NULL,
    licence_name varchar(255) DEFAULT '' NOT NULL,
    licence_url varchar(255) DEFAULT '' NOT NULL
);