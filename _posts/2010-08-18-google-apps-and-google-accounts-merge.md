---
layout: post
title: Google Apps and google Accounts merge
date: 2010-08-18T23:45:00.000Z
tags:
  - information
  - google
  - google apps
  - how-to
image: '/assets/files/2010-08-18-google-apps.png'
image-alt: Google Apps
---
Anyone that has a Google Apps account and wants to access other services like [Google Reader](https://reader.google.com/), [Google Voice](https://voice.google.com/) etc. knows that the username and password of the Google Apps account does not work for these services, since those are not available for Google Apps accounts. To get around this limitation, what you could do (and what I have done in the past) is to sign up for a new Google Account with the same email address as your Google Apps account.  This of course creates confusion at times, duplication of data and disassociation of services. The most obvious example of this is on an Android device. To use my phone, I need to sign in with my Google Apps account. However, to use my Google Voice number, I  have to use sign in again for that service but now using my Google Account (which uses the same email address). This works but I still have to keep two sets of contacts - one for the Google Apps and one in Google Voice.

According to Google, 9 of the top 20 requests from Google Apps customers are for their accounts to work with more services from Google. To facilitate this, the Google Apps account had to be merged with the Google Account. [This page](https://www.google.com/support/accounts/bin/answer.py?hl=en&amp;answer=182174) in Google Apps Help describes the transition.

Unfortunately this was not a simple flip of a switch for Google. [Significant infrastructure changes](https://googleenterprise.blogspot.com/2010/05/more-google-applications-coming-for.html) had to be in place prior to the merging of the accounts. We were promised that this change would be in place by fall and we have not been disappointed. Google is rolling out the update slowly but if you want to 'speed up' the process, you can [sign up](https://spreadsheets1.google.com/a/google.com/viewform?hl=en&amp;formkey=dGdfTTA2eGhFT0c0SDVLXzMzMFNwUUE6MA#gid=0) for an early round of testing of the new infrastructure. I have signed up for several of my Google Apps domains a couple of weeks ago.

I am happy to announce that one of my domains has already gone through the merge process. The domain is BeautyAndTheGeek.IT which is a Google Apps Premier edition. My other domains that are hosted in Google Apps have not transitioned yet and I suspect that Google is first merging Premium accounts, then Educational, Government and it will finish up with the Standard edition of Google Apps, which kind of makes sense (paid customers first!).

#### Email Invitation

<div class="media-body-inline-grid" data-grid="images">
    <div style="display: none">
        <img data-action="zoom" data-width="790" data-height="754" src="/assets/files/2010-08-18-google-apps-merge-001.png">
    </div>
</div>

The merge process works as follows:


- If your Google Apps account email address **has never been used** to access Google products such as Google Reader, Google Voice, Google Code etc. then your Google Apps account will be converted to a Google Account.

- If your Google Apps account email address **has been used** to access Google products such as Google Reader, Google Voice, Google Code etc. then your Google Apps account is conflicting with your Google Account. This page in Google Apps Administrator Help provides an overview of conflicting accounts. In short both of your accounts will merge and the data will be associated with your Google Apps account, which will now be the same as your Google Account.

There are certain limitations to this merge (as one would expect :)). These are as follows:

- If the offline feature is enabled in GMail, the user has to synchronize before the transition. If they don't, they will lose their offline messages.
- Offline Google Calendar doesn't work for Google Apps accounts that have transitioned.
- The following Google products don't work with Google Apps accounts that have transitioned:
 - Android Market for Developers
 - Google Extra Storage (bummer)
 - Health (bummer again)
 - PowerMeter
 - Profiles
 - Web History
 - YouTube
- The purchase of additional Google Storage is currently unavailable for Google Apps accounts. No storage can be purchased for a Google Apps account, although it will be available later this year. If a user account has already purchased extra storage (like I have) in an existing Google Account, the storage will remain in that account and will not transfer to the respective Google Apps account after the transition.
- Delegating email only works with the same account type (i.e. Google Apps)
- Any Picasa Web Album, Profile, or Wave usernames cannot be moved from an existing account to your Google Apps account.

Resources: [Transition readiness checklist](https://www.google.com/support/a/bin/answer.py?answer=182034), Conflicting accounts, Early adopters, [Additional storage for Google Apps](https://www.google.com/support/a/bin/answer.py?answer=172732), Google Apps Administrator Help Center

#### The process

##### Google Apps - Dashboard Warning

<div class="media-body-inline-grid" data-grid="images">
    <div style="display: none">
        <img data-action="zoom" src="/assets/files/2010-08-18-google-apps-merge-002.png">
    </div>
</div>

Once I logged in my Google Apps Administration area, the Dashboard presented a new notice at the top. In short the notice states that new services will be available to the Google Apps accounts. This will be achieved with the transition of the Google Apps Account to a Google Account. The update is free and Google will automatically roll it out on **September 30, 2010**.

Resource: [Google Apps core suite](https://www.google.com/apps/intl/en/business/index.html)

##### Google Apps - Understand the Transition

<div class="media-body-inline-grid" data-grid="images">
    <div style="display: none">
        <img data-action="zoom" src="/assets/files/2010-08-18-google-apps-merge-003.png">
    </div>
</div>

Clicking the **"Get Started"** button at the bottom of the notice will start a wizard that helps with the transition of the domain's accounts to Google accounts of your users (some or all). The first screen contains information that aids in understanding the transition and what is involved.   Moving to the new infrastructure will update your control panel and give you control over which Google services your users can access with their accounts.

Resource: [Transition readiness checklist](https://www.google.com/support/a/bin/answer.py?answer=182034), Google Apps Administrator Help Center

##### Google Apps - New Services

<div class="media-body-inline-grid" data-grid="images">
    <div style="display: none">
        <img data-action="zoom" src="/assets/files/2010-08-18-google-apps-merge-004.png">
    </div>
</div>

Based on the services that are available for your domain user accounts, the screen above might be slightly different. It does however give you (the administrator) the ability to enable or disable services for your users. Note that turning off a service will disallow your users to sign up for that service using their Google Apps account for your domain. It will however not stop them from using a totally different Google Account (personal for instance) to access that service.

##### Google Apps - Notify Conflicting Accounts

<div class="media-body-inline-grid" data-grid="images">
    <div style="display: none">
        <img data-action="zoom" src="/assets/files/2010-08-18-google-apps-merge-005.png">
    </div>
</div>

Confirming the new services, the wizard will display a new screen, that will allow you to notify those users that have conflicting accounts. This screen provides information on how many users have conflicting accounts (in my case only 1), what happens to those accounts (the merge process) and what should you do as the administrator. Google will not provide information on which of your users have conflicting accounts. What they will however do, is offer you a temporary email address and an email template, that you can use to email your users. The email template is shown in the "<em>Google Apps - Email to User</em>" section below.

##### Google Apps - Select Users

<div class="media-body-inline-grid" data-grid="images">
    <div style="display: none">
        <img data-action="zoom" src="/assets/files/2010-08-18-google-apps-merge-006.png">
    </div>
</div>

Clicking **Continue**, brings up the User Selection screen. Here the administrator can select whose account will be transitioned for now. You can choose to pilot test this transition with a small set of users or everyone. The difference is that if you choose a small set of users to pilot test this transition, you can revert their accounts back to what they were prior to this step. If you choose "<em>Everyone</em>" from this screen, the change will be across the organization and cannot be undone. Reminder here that the transition will happen either way by the end of September, 2010. Finally you have the option to inform the users when their account transition is complete. This generates an email (in English) to the user with relevant information. You can see the email in section "<em>Google Apps - Email to User</em>" below.

Resource: Early adopters


##### Google Apps - Confirmation

<div class="media-body-inline-grid" data-grid="images">
    <div style="display: none">
        <img data-action="zoom" src="/assets/files/2010-08-18-google-apps-merge-007.png">
    </div>
</div>

Clicking **Continue** again will bring up the final screen which is the confirmation page. Google is very thorough and I like the confirmation screen. The information in this screen is a summary of what the wizard collected in previous steps. You (the administrator) will have to confirm in several places that you have understood the process, read the agreement and then accept the whole process. Clicking **I accept. Start the transition**. will make things happen :)

Resources: [Transition readiness checklist](https://www.google.com/support/a/bin/answer.py?answer=182034)

##### Google Apps - Transition in progress

<div class="media-body-inline-grid" data-grid="images">
    <div style="display: none">
        <img data-action="zoom" src="/assets/files/2010-08-18-google-apps-merge-008.png">
    </div>
</div>


Navigating back to the dashboard, you will see a message notifying you that there is a transition in place for the accounts of your domain and can take up to 24 hours to be completed. This serves as a reminder on what has just happened. The notice will disappear once the transition is completed.

##### Google Apps - Email to User

<div class="media-body-inline-grid" data-grid="images">
    <div style="display: none">
        <img data-action="zoom" src="/assets/files/2010-08-18-google-apps-merge-009.png">
    </div>
</div>

If you (the administrator) chose to notify your users of this transition (see step *Select Users* above), they will receive an email like the one shown above. If you chose to notify your users with a different message then that message should have been sent to the temporary email address that Google has provided (see step *Notifying Conflicting Accounts* above)

Resources: [Data ownership](https://www.google.com/support/accounts/bin/answer.py?answer=181692), [Transition readiness checklist](https://www.google.com/support/a/bin/answer.py?answer=182034), Google Apps Administrator Help Center

##### Google Apps - Email to User after Transition

<div class="media-body-inline-grid" data-grid="images">
    <div style="display: none">
        <img data-action="zoom" src="/assets/files/2010-08-18-google-apps-merge-010.png">
    </div>
</div>

Finally another email will be sent to the user summarizing what has happened with their account. This email contains links to additional resources.

Resources: [List of products](https://www.google.com/options/), [New sign in option](https://www.google.com/support/accounts/bin/answer.py?answer=181703),  Data ownership, What is a browser?, [Using multiple accounts](https://www.google.com/support/accounts/bin/answer.py?hl=en&amp;answer=182343), Help Center for Admins, Google Apps Administrator Help Center


##### Google Apps - User Login message for Account Merge

<div class="media-body-inline-grid" data-grid="images">
    <div style="display: none">
        <img data-action="zoom" src="/assets/files/2010-08-18-google-apps-merge-011.png">
    </div>
</div>

Once the transition operation is completed, the next time the user logs in their account, they will see the screen above. In short this screen informs the user of what has happened and provides links to online resources for help. The user must click **I accept. Continue to my account** for them to have access to their account. This is  a one off process.


Resources: Google Apps Administrator Help Center, [Google Security and Privacy](https://www.google.com/support/a/bin/answer.py?hl=en&amp;answer=60762), [Using multiple accounts](https://www.google.com/support/accounts/bin/answer.py?hl=en&amp;answer=182343), [Terms of Service](https://www.google.com/intl/en/policies/terms/)

#### Conclusion
Without a doubt this is a huge step forward for Google and for us as admins or users. The ability to have a single sign on to use their products not only helps them but us by simplifying everything. That also means that if a hacker guesses that my password is **password1** they have access to all my Google related services... but oh well :)

Joking apart though, I am really excited that this change has finally occurred. I am guessing that the next steps would be to give users or admins the ability to purchase additional storage for a single account is something that in my view will make quite a lot of money for Google. A lot of people will want to keep all their emails and are nearing or have reached their email quota. Administrators would be happy to pay for certain users that have depleted their email storage allocation without converting their whole Google Apps edition to the Premier one. After all, if your organization has 100 users and 5 of them are near capacity, even if you pay $20 per user it will cost $100 per year. If however the whole account is changed to a Premier account (with 25Gb per mailbox) the cost will go up to $5,000.

Some questions come to mind:

- Will the user be able to purchase storage for all services or just for email?
- Will the 1Gb limit from Docs be lifted and will it be combined with storage for mail?
- $20 a year gives you 80Gb in Picasa. Will Google follow that model or change it? According to the [Additional storage for Google Apps](https://www.google.com/support/a/bin/answer.py?answer=172732) page it appears that it will be more expensive now to have more space.
- If storage is so cheap (Picasa) will it be extended to Google Documents? It appears that 7.5Gb is enough for the vast majority of users as far as email is concerned, but 1Gb for Docs is not enough. For a society moving towards a full electronic document storage this could help a lot.

Still a long way to go until everything clears up. I am not sure that Google has all the answers yet but they are moving forward and this is the most encouraging news of all!
