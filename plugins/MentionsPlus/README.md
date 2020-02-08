Mentions+
=========
   
Gardens mentioning feature doesn't work with user names that include spaces or foreign characters. This plugin is for those who need such a feature.    
    
With Mentions+ you can mention user with spaces in their user names with escaping characters: **@"test user"**. You could even choose if you want differing beginning and ending escape characters, which makes something like that possible: **@{test user}**.  It's completely up to you how your users could use this feature!   
   
It goes without question that special characters doesn't have to be escaped! Just use the mention feature as you are used to: **@äüöß** would be converted to a link to the user with that strange name and notice him about being mentioned. Simple as that.

Enabling the plugin alone won't get you started
-----------------------------------------------
By now you have to do some manual work. Add the following line to the file /conf/bootstrap.after.php:   
`Gdn::FactoryInstall('MentionsFormatter', 'UmlautMentionsFormatter', NULL, Gdn::FactoryInstance);`
If that file does not exist right now, copy the file `bootstrap.after.php.template` from plugin directoy to the directory `/conf` and rename it to `bootstrap.after.php`

After you have it working, you should configure the plugin. Go to its settings and look if the existing settings adhere to the naming rules of your users. You can enter
- allowed characters
- min/max length of user names
- beginning escape character
- ending escape character
- and also custom code for /me action but that feature isn't tested!

Allowed characters and min/max length are part of regular expressions. If you do not know what that is or if you are unsure how to use them, ask for help at vanillaforums.org!
