Gentoo is one of my favorite Linux distributions. Although I am comfortable with other distributions, Gentoo has a special place in my heart and whenever I can use it I do :)
<img class="post-image" src="{{ cdnUrl }}/files/gentoo.png" />

There are however some times that I would like to install a package - mostly to test something - and the package is masked. Masked packages are not "production ready" so they are not included in the portage tree i.e. available to be installed.

To allow a masked package to be installed, you will need to unmask that package by adding a corresponding entry in the `etc/portage/package.keywords` file.

The problem happens when the masked package (that you just unmasked) depends on other packages that are also masked. You will then need to rinse and repeat the process to ensure that everything is in place so that you can install that unmasked package.

> **NOTE: Playing with masked packages is like playing with fire. If you don't know what you are doing or you are not ready to potentially have an unusable system, don't follow the instructions below or unmask any packages.**

##### `app-portage/autounmask`

The Gentoo developers have created a little utility that will unmask each package that needs to be unmasked. The utility is `app-portage/autounmask`.

I wanted to unmask `www-misc/fcgiwrap` so my manual method would be:

```sh
echo "=www-misc/fcgiwrap ~amd64" >> /etc/portage/packages.keywords
```

and would then emerge the package

```sh
emerge =www-misc/fcgiwrap
```

Instead I used `autounmask`:

```sh
emerge app-portage/autounmask
```

and

```sh
autounmask www-misc/fcgiwrap-1.0.3

autounmask version 0.27 (using PortageXS-0.02.09 and portage-2.1.9.42)
* Using repository: /usr/portage
* Using package.keywords file: /etc/portage/package.keywords
* Using package.unmask file: /etc/portage/package.unmask
* Using package.use file: /etc/portage/package.use
* Unmasking www-misc/fcgiwrap-1.0.3 and its dependencies.. this might take a while..
* Added '=www-misc/fcgiwrap-1.0.3 ~amd64' to /etc/portage/package.keywords
* done!
```

Once that is done I can issue the emerge command and voila!

```sh
emerge www-misc/fcgiwrap
```

Although in my case there was only one dependency to unmask, when trying to unmask packages that have multiple dependencies such as gnome, kde etc., `autounmask` can be a very helpful utility.