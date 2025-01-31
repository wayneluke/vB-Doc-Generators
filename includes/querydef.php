<?php

// Query Definitions

class QueryDefs {

    protected $version = "SELECT `value` FROM {prefix}setting WHERE varname='templateversion'";

    protected $modules = [
            'categories'      => "SELECT distinct(category) FROM {prefix}widget WHERE category != 'Abstract';",
            'widget'          => "SELECT * FROM {prefix}widget WHERE template!='' AND isthirdparty=0 AND category = ?;",
            'templatephrase'  => "SELECT p.text FROM {prefix}widget AS w 
                                  LEFT JOIN {prefix}phrase AS p ON (p.varname = concat(w.template,'_widgettitle')) 
                                  WHERE w.widgetid=?;",
            'titlephrase'     => "SELECT p.text FROM {prefix}widget AS w 
                                  LEFT JOIN {prefix}phrase AS p ON (p.varname = w.titlephrase) 
                                  WHERE w.widgetid=?;",
            'definitions'     => "",
    ];

    protected $options = [
            'groups'          => "SELECT sg.*, p.text AS title FROM {prefix}settinggroup AS sg 
                                  LEFT JOIN {prefix}phrase AS p ON (p.varname LIKE CONCAT('settinggroup_',sg.grouptitle)) 
                                  WHERE sg.product='vbulletin' AND p.languageid=-1 
                                  ORDER BY sg.displayorder",
            'settings'        => "SELECT p.text AS 'title', p2.text AS 'description', 
                                    s.varname, s.defaultvalue, s.datatype, s.displayorder FROM {prefix}setting AS s 
                                  LEFT JOIN {prefix}settinggroup AS sg ON (s.grouptitle = sg.grouptitle) 
                                  LEFT JOIN {prefix}phrase AS p ON (p.varname LIKE CONCAT('setting_', s.varname, '_title') AND p.languageid=-1) 
                                  LEFT JOIN {prefix}phrase AS p2 ON (p2.varname LIKE CONCAT('setting_', s.varname, '_desc') AND p2.languageid=-1) 
                                  WHERE s.grouptitle=?
                                  ORDER BY s.displayorder",
            'help'             => "SELECT text FROM phrase WHERE varname LIKE CONCAT('options_options_',?,'_text') AND phrase.languageid=-1"
    ];

    protected $pages = [];

    protected $permissions = [];


    protected $stylevars = [
            'groups'          => "SELECT DISTINCT(stylevargroup) FROM {prefix}stylevardfn 
                                  ORDER BY stylevargroup ASC;",
            'groupdesc'       => "SELECT `text` FROM `phrase` WHERE `varname`=concat('doc_stylevar_group_',?)",
            'stylevars'       => "SELECT p1.text AS 'title', p2.text AS 'description', s.* FROM {prefix}stylevardfn AS s
                                  LEFT JOIN {prefix}phrase AS p1 ON (p1.varname LIKE CONCAT('stylevar_', s.stylevarid, '_name') AND p1.languageid=-1) 
                                  LEFT JOIN {prefix}phrase AS p2 ON (p2.varname LIKE CONCAT('stylevar_', s.stylevarid, '_description') AND p2.languageid=-1) 
                                  WHERE stylevargroup=? ORDER BY s.stylevarid ASC", 
            'default_value'   => "SELECT value FROM {prefix}stylevar WHERE stylevarid=? AND styleid=-1",
    ];

    protected $userhelp = [
            'sections'        => "SELECT f.*, p.text AS 'title' FROM {prefix}faq AS f
                                  LEFT JOIN {prefix}phrase AS p ON (p.varname LIKE CONCAT(f.faqname, '_gfaqtitle'))
                                  WHERE faqparent = 'faqroot'
                                  ORDER BY displayorder",
            'pages'           => "SELECT f.*, p.text as 'title', p2.text as 'text' from {prefix}faq AS f
                                  LEFT JOIN {prefix}phrase AS p ON (p.varname LIKE CONCAT(f.faqname, '_gfaqtitle'))
                                  LEFT JOIN {prefix}phrase AS p2 ON (p2.varname LIKE CONCAT(f.faqname,'_gfaqtext'))
                                  WHERE faqparent = ?",
    ];

    public function getQueries($property) 
    {
        if (property_exists($this, $property)) {
          return $this->$property;
        }
    }

    public function getVersion($db)
    {
        return $db->run_query($this->version)->fetchColumn();
    }
}