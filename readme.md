# Updates

### v1.0.1

- added features:
    - yml export:
        run <code>attribute:export-type</code> command to export all the form types from the database to separated .yml configs
        optionally use the <code>path</code> argument to define a directory where to save. By default <code>%kernel.cache_dir%/export/form_type/</code> will be used as destination folder.
    - yml import:
         run <code>attribute:import-type</code> command to import form types from .yml
         by default the command will scan all the bundles <code>/Resources/config/form</code> directory, and process the ymls found there. If you want to import only one configuration yml, than run the command with the optional <code>path</code> argument, which points to a valid yml file.
    - <code>name</code> field of Type entity is now unique