---
layout: page
title: Email Setup
permalink: /email-setup
---

## Overview
You can use the guide below to set up your email clients with [PrivateEmail](https://privateemail.com).

## General
PrivateEmail supports only encrypted connections. The connection information is:

**Username**: your email address
**Password**: password for this email account
**Incoming/outgoing servers name**: mail.privateemail.com

**Incoming server type**: IMAP or POP3
**Incoming server (IMAP)**: 993 port for SSL, 143 for TLS/STARTTLS
**Incoming server (POP3)**: 995 port for SSL

**Outgoing server (SMTP)**: 465 port for SSL, 587 for TLS/STARTTLS
Outgoing server authentication should be switched on, SPA (secure password authentication) must be disabled.

## Outlook 2019
### New Account

1. Open Microsoft Outlook 2019 on your device.
2. If you don't have any mailboxes added yet, you will see the **Account Information page**. Click the **+ Add Account** button there.
If you have mailboxes set up already, go to **File** tab **>> Info >> Account Information** and click the **+ Add Account** button:

![](/assets/files/email/pm-outlook-2019-10)

![](/assets/files/email/pm-outlook-2019-11)

3. Type in your full Private Email address and click Connect:

![](/assets/files/email/pm-outlook-2019-12)

Disregard any warnings that may appear, and proceed to the next step.

4. Type in the password you are using for this email address and click Connect:

![](/assets/files/email/pm-outlook-2019-13)

If you are not able to proceed, you may need to repeat the process using the manual setup. The detailed instructions can be found below.

5. Select the Change account settings option and click Next. On the next page, select the POP or IMAP option:

![](/assets/files/email/pm-outlook-2019-14)

**POP3** stands for Post Office Protocol, and was designed as a simple way to access a remote email server. POP works by downloading your emails from your provider's mail server and then marking them for deletion there. This means you can only ever read those email messages in that email client, on that computer. You will not be able to access any previously downloaded emails from any other device, or with any other email client, or through webmail.

**IMAP** stands for Internet Message Access Protocol, and was designed specifically to eliminate the limitations of POP. IMAP allows you to access your emails from any client, and any device, and webmail login at any time, until you delete them. You can also use different devices and email/webmail clients to access the same mailbox and check, send and receive email, which is not available with POP3 connection.

6. If you choose IMAP account setup, enter the following server details:

* **Incoming mail server**:  mail.privateemail.com (same for all accounts)
* **Incoming mail port**: 993 (or 143 with STARTTLS)
* **Encryption method**: SSL/TLS (or STARTTLS if 143 port is used)
* **Require logon using Secure Password Authentication (SPA)**: should be unchecked

* **Outgoing mail server**:  mail.privateemail.com (same for all accounts)
* **Outgoing mail port**: 465 (or 587 with STARTTLS)
* **Encryption method**: SSL/TLS (or STARTTLS if 587 port is used)
* **Require logon using Secure Password Authentication (SPA)**: should be unchecked

![](/assets/files/email/pm-outlook-2019-15)

7. If you choose POP account setup, enter the following server details:

* **Incoming mail server**:  mail.privateemail.com (same for all accounts)
* **Incoming mail port**: 995 (or 110 with STARTTLS)
* **This server requires an encrypted connection(SSL/TLS)**: should be checked
* **Require logon using Secure Password Authentication (SPA)**: should be unchecked

* **Outgoing mail server**:  mail.privateemail.com (same for all accounts)
* **Outgoing mail port**: 465 (or 587 with STARTTLS)
* **Encryption method**: SSL/TLS (or STARTTLS if 587 port is used)
* **Require logon using Secure Password Authentication (SPA)**: should be unchecked

![](/assets/files/email/pm-outlook-2019-16)

8. Once all the details are filled, click **Next**.

9. After that, type in your password for this Private Email account and click **Connect**:

![](/assets/files/email/pm-outlook-2019-17)

10. If all the settings are correct, you will receive the following message.

![](/assets/files/email/pm-outlook-2019-18)

Click **Done** to complete the setup.

### Existing Account

In order to check or update the configuration of an existing email account, follow these steps:

1. Open Microsoft Outlook 2019 on your device.

2. Go to the **File** tab **>> Info >> Account Information** page and click on **Account Settings**:

![](/assets/files/email/pm-outlook-2019-19)

3. Select the mailbox in question and click Repair:

![](/assets/files/email/pm-outlook-2019-20)

4. In the next window, click on Advanced options, check Let me repair my account manually and click on Repair:

![](/assets/files/email/pm-outlook-2019-21)

5. On the next page, you will see incoming and outgoing server settings.
Make sure all of them are set properly. If any information is set incorrectly, update it for both Incoming mail and Outgoing mail configurations:

![](/assets/files/email/pm-outlook-2019-22)

6. Once all details for the outgoing connection are updated, click Repair:

![](/assets/files/email/pm-outlook-2019-23)


## Thunderbird

PrivateEmail service supports autoconfig feature, which allows to set up email account automatically in Thunderbird.

1. Go to **File > New > Existing Mail Account**:

![](/assets/files/email/pm-outlook-2019-30)


2. In **Mail Account Setup** window enter the following details:

**Your Name**: the name you would like the recipients of your emails to see
**E-Mail Address**: your full Private Email address
**Password**: password for your Private Email account.

3. Once all the fields are filled, click **Continue**:

![](/assets/files/email/pm-outlook-2019-31)

4. You will see **Configuration found at email provider** message.
Make sure that your Incoming and Outgoing configuration was detected properly and matches the one you can see on the screenshot below:

![](/assets/files/email/pm-outlook-2019-32)

Click **Done** if it is the same.

If you do not see **Configuration found at email provider message** or if configuration was not detected properly for some reason, you can complete the first two steps and continue configuring Thunderbird **manually** in this way:

1. After the first two steps are completed you will either see manual config window (in case Thunderbird fails to detect configuration automatically) or will need to click on **Manual Config** to see that window:

![](/assets/files/email/pm-outlook-2019-33)

2. In the next screen, select **POP3** or **IMAP** from the Account Type list, and enter your details as follows:

For **IMAP** protocol use the following settings:

**Incoming and Outgoing server**: mail.privateemail.com

| Protocol | Port | SSL     | Authentication  |
|----------|------|---------|-----------------|
| IMAP     | 993  | SSL/TLS | Normal Password |
| SMTP     | 465  | SSL/TLS | Normal Password |

![](/assets/files/email/pm-outlook-2019-34)

For **POP3** protocol use the following settings:

**Incoming and Outgoing server**: mail.privateemail.com

| Protocol | Port | SSL     | Authentication  |
|----------|------|---------|-----------------|
| POP3     | 995  | SSL/TLS | Normal Password |
| SMTP     | 465  | SSL/TLS | Normal Password |

![](/assets/files/email/pm-outlook-2019-35)

Alternatively, you can use following settings for **IMAP** protocol:

| Protocol | Port | SSL     | Authentication  |
|----------|------|---------|-----------------|
| IMAP     | 143  | None    | Normal Password |
| SMTP     | 587  | None    | Normal Password |

For **POP3** protocol:

| Protocol | Port | SSL     | Authentication  |
|----------|------|---------|-----------------|
| POP3     | 995  | SSL/TLS | Normal Password |
| SMTP     | 587  | None    | Normal Password |

NOTE: if Add Security Exception window appears, click Confirm Security Exception button.

**Advanced Settings**
 
Once your account has been created, you will be able to choose additional settings for **IMAP** or **POP3** incoming/outgoing server.

1. Right-Click on your account in the list of accounts:

![](/assets/files/email/pm-outlook-2019-36)

2. To manage incoming server, go to **Server Settings**:

![](/assets/files/email/pm-outlook-2019-37)

3. If you need to change settings for outgoing server, select **Outgoing Server (SMTP)**:

![](/assets/files/email/pm-outlook-2019-38)

Here you can find your current settings for accounts you have and click on **Edit...** to make changes in the next window:

![](/assets/files/email/pm-outlook-2019-39)

