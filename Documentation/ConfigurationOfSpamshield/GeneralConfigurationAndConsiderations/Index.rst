.. include:: Images.txt

.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. ==================================================
.. DEFINE SOME TEXTROLES
.. --------------------------------------------------
.. role::   underline
.. role::   typoscript(code)
.. role::   ts(typoscript)
   :class:  typoscript
.. role::   php(code)


General configuration and considerations
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

With the help of the TypoScript constants you can configure the
following settings  **for each supported extension** :

- Which spam checks should be applied? As you can see from
  the listing above the different extensions do not support all of the
  implemented checks.

- How many positive spam checks are needed to mark the submitted entry
  as spam? By default only 1 check has to fail.

Furthermore you can configure some settings globally. Before
wt\_spamshield 1.2.0 this was done within the extension manager (see
settings below).


**General configuration**
"""""""""""""""""""""""""

The following configuration can be set via TypoScript constants.

.. ### BEGIN~OF~TABLE ###

.. container:: table-row

         **Title**

         **Explanation**

         **Default value**


.. container:: table-row

         logging.notificationAddress

         Enter an email address if you would like to receive an email if spam
         was recognized (e.g. email@domain.org).

         

.. container:: table-row

         logging.pid

         Enter a PID for saving spam log entries (-2 for current page, -1
         deactivates logging, 0 for root page, 1 for PID 1 etc.).

         0


.. container:: table-row

         comments

         Enable spamshield for comments

         0


.. container:: table-row

         mailform

         Enable spamshield for default mailform

         0


.. container:: table-row

         direct\_mail\_subscription

         Enable spamshield for direct\_mail\_subscription

         0


.. container:: table-row

         ke\_userregister

         Enable spamshield for ke\_userregister

         0


.. container:: table-row

         powermail

         Enable spamshield for powermail (version 1.x)

         0


.. container:: table-row

         powermail2

         Enable spamshield for powermail (version 2.x)

         0


.. container:: table-row

         t3\_blog

         Enable spamshield for t3\_blog

         0


.. container:: table-row

         ve\_guestbook

         Enable spamshield for ve\_guestbook

         0


.. container:: table-row

         pbsurvey

         Enable spamshield for pbsurvey

         0


.. container:: table-row

         formhandler

         Enable spamshield for formhandler

         0


.. container:: table-row

         validators.standardMailform\_new.enable

         validators for standardMailform >= TYPO3 4.6:

         blacklistCheck, httpCheck, honeypotCheck


.. container:: table-row

         validators.standardMailform\_new.how\_many\_validators\_can\_fail

         failure rate for standardMailform >= TYPO3 4.6, i.e. how many
         validators can fail

         0


.. container:: table-row

         validators.standardMailform\_old.enable

         validators for standardMailform <= TYPO3 4.5

         blacklistCheck, httpCheck, uniqueCheck, sessionCheck, honeypotCheck


.. container:: table-row

         validators.standardMailform\_old.how\_many\_validators\_can\_fail

         failure rate for standardMailform <= TYPO3 4.5, i.e. how many
         validators can fail

         0


.. container:: table-row

         validators.powermail.enable

         validators for powermail

         blacklistCheck, sessionCheck, httpCheck, uniqueCheck, honeypotCheck, AkismetCheck


.. container:: table-row

         validators.powermail.how\_many\_validators\_can\_fail

         failure rate for powermail, i.e. how many validators can fail

         0


.. container:: table-row

         validators.powermail2.enable

         validators for powermail2

         AkismetCheck, blacklistCheck


.. container:: table-row

         validators.powermail2.how\_many\_validators\_can\_fail

         failure rate for powermail2, i.e. how many validators can fail

         0


.. container:: table-row

         validators.ve\_guestbook.enable

         validators for ve\_guestbook

         blacklistCheck, nameCheck, sessionCheck, httpCheck, honeypotCheck, AkismetCheck


.. container:: table-row

         validators.ve\_guestbook.how\_many\_validators\_can\_fail

         failure rate for ve\_guestbook, i.e. how many validators can fail

         0


.. container:: table-row

         validators.comments.enable

         validators for comments

         blacklistCheck, nameCheck, httpCheck, sessionCheck, honeypotCheck, AkismetCheck


.. container:: table-row

         validators.comments.how\_many\_validators\_can\_fail

         failure rate for comments, i.e. how many validators can fail

         0


.. container:: table-row

         validators.t3\_blog.enable

         validators for t3\_blog

         httpCheck, akismetCheck


.. container:: table-row

         validators.t3\_blog.how\_many\_validators\_can\_fail

         failure rate for comments, i.e. how many validators can fail

         0


.. container:: table-row

         validators.direct\_mail\_subscription.enable

         validators for direct\_mail\_subscription

         blacklistCheck, httpCheck, uniqueCheck, honeypotCheck


.. container:: table-row

         validators.direct\_mail\_subscription.how\_many\_validators\_can\_fail

         failure rate for comments, i.e. how many validators can fail

         0


.. container:: table-row

         validators.ke\_userregister.enable

         validators for ke\_userregister

         blacklistCheck, nameCheck, httpCheck, sessionCheck, honeypotCheck, AkismetCheck


.. container:: table-row

         validators.ke\_userregister.how\_many\_validators\_can\_fail

         failure rate for comments, i.e. how many validators can fail

         0


.. container:: table-row

         validators.pbsurvey.enable

         validators for pbsurvey

         httpCheck, sessionCheck, honeypotCheck, blacklistCheck


.. container:: table-row

         validators.pbsurvey.how\_many\_validators\_can\_fail

         failure rate for comments, i.e. how many validators can fail

         0


.. container:: table-row

         validators.formhandler.enable

         validators for formhandler

         blacklistCheck, httpCheck, uniqueCheck, honeypotCheck, AkismetCheck


.. container:: table-row

         validators.formhandler.how\_many\_validators\_can\_fail

         failure rate for comments, i.e. how many validators can fail

         0


.. container:: table-row

         redirect\_mailform

         Mailform Redirect: Redirect URL for default mailform

         


.. container:: table-row

         redirect\_ve\_guestbook

         ve\_guestbook Redirect: Redirect PID for ve\_guestbook

         


.. container:: table-row

         httpCheck.maximumLinkAmount

         Set the maximum number of links (http, https, ftp) within a message.
         If you want to allow 3 links enter "3". If you want no links at all
         enter "0".

         3


.. container:: table-row

         uniqueCheck.fields

         Enter different field names (separated by comma) which should not be
         equal. Example for powermail: uid1 = first name and uid2 = last name
         -> "uid1,uid2". You can add more than one condition by splitting them
         with semicolons. Example for powermail: uid1 = first name, uid2 = last
         name, uid3 = address, uid1 and uid3 should not be equal as well as
         uid2 and uid3 should not be equal but uid1 and uid2 can be equal ->
         "uid1,uid3[semicolon]uid2,uid3".


.. container:: table-row

         akismetCheck.akismetKey

         Enter your Akismet key to activate Akismet check (signup at
         https://akismet.com/signup/).


.. container:: table-row

         sessionCheck.sessionStartTime

         Minimum time frame between entering the form page and submiting the
         form. 0 for disable.

         10


.. container:: table-row

         sessionCheck.sessionEndTime

         Maximum time frame between entering the form page and submiting the
         form. 0 for disable.

         600


.. container:: table-row

         honeypot.css.inputStyle

         CSS style for honeypot input field

         style="position:absolute; margin:0 0 0 -999em;"


.. container:: table-row

         honeypot.css.inputClass

         CSS class for honeypot input field

         class="wt\_spamshield\_field wt\_spamshield\_honey"


.. container:: table-row

         honeypot.additionalParams.standard

         additional tag params for honeypot input field

         autocomplete="off"


.. container:: table-row

         honeypot.additionalParams.html5

         additional tags params for honeypot input field when using HTML5 as doctype
         
         The standard additional params will always be rendered
         (honeypot.additionalParams.standard). If you're using HTML5 as doctype
         the value of honeypot.additionalParams.html5 is rendered as well. If
         you are not using HTML5 as doctype you can easily add the tabindex
         setting to honeypot.additionalParams.standard in your own constants.
         Please consider that negative values for tabindex are only valid in
         HTML5. Even if it does not validate in XHTML or HTML < 5 newer
         browsers will understand it.

         tabindex="-1"


.. container:: table-row

         honeypot.inputname.comments

         Honeypot input name for comments

         uid987651


.. container:: table-row

         honeypot.inputname.direct\_mail\_subscription

         Honeypot input name for direct\_mail\_subscription

         uid987651


.. container:: table-row

         honeypot.inputname.standardMailform

         Honeypot input name for standardMailform

         uid987651


.. container:: table-row

         honeypot.inputname.powermail

         Honeypot input name for powermail

         uid987651


.. container:: table-row

         honeypot.inputname.ve\_guestbook

         Honeypot input name for ve\_guestbook

         uid987651


.. container:: table-row

         honeypot.inputname.ke\_userregister

         Honeypot input name for ke\_userregister

         uid987651


.. container:: table-row

         honeypot.inputname.pbsurvey

         Honeypot input name for pbsurvey

         uid987651


.. container:: table-row

         honeypot.inputname.formhandler

         Honeyput input name for formhandler

         uid987651


.. ###### END~OF~TABLE ######

The following screenshot shows some settings of wt\_spamshield within
the Constant Editor.

|img-6|


Example for powermail 1.x
"""""""""""""""""""""""""

::

   plugin.wt_spamshield {
     validators.powermail.enable = blacklistCheck, sessionCheck, httpCheck, honeypotCheck, akismetCheck
     validators.powermail.how_many_validators_can_fail = 1
   }

The example above configures the integration of powermail 1.x. By
default the following checks are available: blacklistCheck,
sessionCheck, httpCheck, uniqueCheck, honeypotCheck, akismetCheck. In
the example we have removed the uniqueCheck. Furthermore we have risen
the number of positive spam checks (how\_many\_validators\_can\_fail).
Now 2 checks have to fail in order to mark the entry as spam.