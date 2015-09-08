## Create a SSL Certificate in Linux

<img class="post-image" src="{{ cdnUrl }}/files/2010-01-06-design-patterns.png" />

There are times that I want to set up a secure communication with the server I am working on. This might be because I want to run phpMyAdmin over SSL (I do not like unencrypted communications over the Internet), install a certificate for an eShop for a client or just for my personal use.

The first time I did this, I had to research on the Internet and after a bit of a trial and error I managed to get everything working. However if you do not do something on a regular basis you will forget. I am no exception to this rule hence this post to allow me to remember what I did and hopefully help you too.

### Prerequisites:

This how-to assumes that you are running Gentoo, however these instructions can easily be applied to any other Linux distribution.

I need to check if [openssl](http://www.openssl.org/) is installed:

```sh
vanadium ~ # emerge --pretend dev-libs/openssl

These are the packages that would be merged, in order:

Calculating dependencies... done!
[ebuild  R  ] dev-libs/openssl-0.9.8l-r2
```

If you do not see the `[R]` next to the package (and you see a `N` for instance) that means that you need to install the package. Issuing:

```sh
vanadium  ~ # emerge --verbose dev-libs/openssl
```

will do the trick.

### Generate the Private Key

I like to generate keys with a lot of bits. All of my certificates have 4096 bits. This is a personal preference and it does not hurt to keep that value. Your host or Signing Authority (like GoDaddy, VeriSign, Thawte etc.) might ask you in their instructions to generate one with 2048 bits so don't be alarmed there.

Creating the RSA private key with 4096 bits using Triple-DES:

```sh
vanadium ~ # openssl genrsa -des3 -out /root/vanadium.niden.net.locked.key 4096
Generating RSA private key, 4096 bit long modulus
.............................................................++
...........++
e is 65537 (0x10001)
Enter pass phrase for /root/vanadium.niden.net.locked.key:
Verifying - Enter pass phrase for /root/vanadium.niden.net.locked.key:
```

### Remove the passphrase from the Private Key

The key that was created earlier has a passphrase. Although this is good, it does have a side effect that any web server administrator does not like - the passphrase itself. Once the certificate is installed using the key (with the passphrase), every time that Apache is restarted, it will prompt the operator for the passphrase. This can be very inconvenient if your web server reboots in the middle of the night. Since Apache will be waiting for the passphrase, your site will be inaccessible.

To avoid this inconvenience, I am removing the passphrase from the key. If you noticed the key that I have created above has the 'locked' phrase in its name. The reason is that I know that that particular key has the passphrase on it. I first need to copy the key and then remove the passphrase:

```sh
vanadium ~ # cp -v vanadium.niden.net.locked.key vanadium.niden.net.key
`vanadium.niden.net.locked.key' -> `vanadium.niden.net.key'
vanadium ~ # openssl rsa -in vanadium.niden.net.locked.key -out vanadium.niden.net.key
Enter pass phrase for vanadium.niden.net.locked.key:
writing RSA key
```

### Generate the Certificate Signing Request (CSR)

The purpose of the CSR is to be sent to one of the Certificate Authorities (GoDaddy, VeriSign, Thawte etc.) for verification. Alternatively I can self-sign the CSR (see below).

Upon generation of this CSR I am asked about particular pieces of information to be incorporated in the CSR. The most important piece of information that I need to ensure that is correct is the **Common Name</strong>**. The answer to that question has to be the name of my web server - `vanadium.niden.net` in my case.

**NOTE**: I am using the key without the passphrase.

The command to generate the CSR is as follows:

```sh
vanadium ~ # openssl req -new -key vanadium.niden.net.key -out vanadium.niden.net.csr
You are about to be asked to enter information that will be incorporated
into your certificate request.
What you are about to enter is what is called a Distinguished Name or a DN.
There are quite a few fields but you can leave some blank
For some fields there will be a default value,
If you enter '.', the field will be left blank.
-----
Country Name (2 letter code) [AU]:US
State or Province Name (full name) [Some-State]:Virginia
Locality Name (eg, city) []:Arlington
Organization Name (eg, company) [Internet Widgits Pty Ltd]:niden.net
Organizational Unit Name (eg, section) []:IT
Common Name (eg, YOUR name) []:vanadium.niden.net
Email Address []:domains@niden.net

Please enter the following 'extra' attributes
to be sent with your certificate request
A challenge password []:
An optional company name []:
```

Once this step is completed, I open the CSR file with a text editor, copy the contents and paste them in the relevant field of the Certification Authority (in my case GoDaddy), so that they can verify the CSR and issue the certificate.


If however this is a development box or you do not want your certificate signed by a Certification Authority, you can check the section below on how to generate a self-signed certificate.


### Generating a Self-Signed Certificate

At this point you will need to generate a self-signed certificate because you either don't plan on having your certificate signed by a CA, or you wish to test your new SSL implementation while the CA is signing your certificate. This temporary certificate will generate an error in the client browser to the effect that the signing certificate authority is unknown and not trusted.

To generate a temporary certificate which is good for 365 days, issue the following command:

```sh
vanadium ~ # openssl x509 -req -days 365 -in vanadium.niden.net.csr -signkey vanadium.niden.net.key -out vanadium.niden.net.crt
Signature ok
subject=/C=US/ST=Virginia/L=Arlington/O=niden.net/OU=IT/CN=vanadium.niden.net/emailAddress=domains@niden.net
Getting Private key
```

### Installation

For my system, the certificates are kept under `/etc/apache2/ssl/` so I am going to copy them there (your need to adjust the instructions below to suit your system/installation):

```sh
vanadium ~ # cp -v vanadium.niden.net.key /etc/apache2/ssl/
vanadium ~ # cp -v vanadium.niden.net.crt /etc/apache2/ssl/
```

I also need to open the relevant file to enable the certificate

```sh
vanadium ~ # nano -w /etc/apache2/vhosts.d/00_default_ssl_vhost.conf
```

In that file I need to change the following directives:


<pre>SSLCertificateFile /etc/ssl/apache2/vanadium.niden.net.crt
SSLCertificateKeyFile /etc/ssl/apache2/vanadium.niden.net.key</pre>

If my certificate was issued by a Certificate Authority, the files that I have received have the CA certificate file. I can enable it in the following line:

```sh
SSLCACertificateFile /etc/ssl/apache2/vanadium.niden.net.ca-bundle.crt
```

### Restarting Apache

```sh
vanadium ~ # /etc/init.d/apache2 restart
 * Stopping apache2 ...                              [ ok ]
 * Starting apache2 ...                              [ ok ]
```

Navigating to `https://vanadium.niden.net` should tell me if what I did was successful or not. If your browser (Google Chrome in my case) gives you a bright red screen with all sorts of warnings, that means that

* either you self signed the certificate - in which case it complains about the certificate not being signed by a Certificate Authority or
* you made a mistake and the Common Name in the certificate is not the same as the host name.

Both these errors are easy to fix.
