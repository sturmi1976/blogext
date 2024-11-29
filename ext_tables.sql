CREATE TABLE tt_content (
    tx_blogext_parent INT(11) DEFAULT '0',
);



CREATE TABLE tx_blogext_domain_model_post (
    uid INT AUTO_INCREMENT PRIMARY KEY,
    pid INT DEFAULT '0' NOT NULL,
    tstamp INT DEFAULT '0' NOT NULL,
    crdate INT DEFAULT '0' NOT NULL,
    cruser_id INT DEFAULT '0' NOT NULL,
    deleted TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
    hidden TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
    sorting INT(11) DEFAULT '0' NOT NULL,
    title VARCHAR(255) DEFAULT '' NOT NULL,
    categories VARCHAR(255) DEFAULT '' NOT NULL,
    url_segment varchar(255) DEFAULT '' NOT NULL,
    teaser TEXT,
    seo_title VARCHAR(255) DEFAULT '' NOT NULL,
    description TEXT,
    meta_keywords VARCHAR(255) DEFAULT '' NOT NULL,
    content_elements TEXT,
    author INT(11) DEFAULT 0,
    comments_disable TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
);



CREATE TABLE tx_blogext_domain_model_category (
    uid INT AUTO_INCREMENT PRIMARY KEY,
    pid INT DEFAULT '0' NOT NULL,
    tstamp INT DEFAULT '0' NOT NULL,
    crdate INT DEFAULT '0' NOT NULL,
    cruser_id INT DEFAULT '0' NOT NULL,
    deleted TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
    hidden TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
    sorting INT(11) DEFAULT '0' NOT NULL,
    title VARCHAR(255) DEFAULT '' NOT NULL,
    url_segment varchar(255) DEFAULT '' NOT NULL,
    seo_title VARCHAR(255) DEFAULT '' NOT NULL,
    meta_description TEXT,
    meta_keywords VARCHAR(255) DEFAULT '' NOT NULL,
);