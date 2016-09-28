#Optical Character Recognition (OCR) with PHP and Tesseract

A very simple example application to demonstrate how, using the open-source [Tesseract](https://github.com/tesseract-ocr/tesseract) application, one might integrate OCR using PHP.

To get up-and-running, I'd recommend you use Vagrant. A `Vagrantfile` is provided, based on [Homestead Improved](https://github.com/Swader/homestead_improved).

##Quick Start

Add the following to your hosts file:

```
192.168.10.10		homestead.app
```

Set up the Virtual Machine using Vagrant:

```bash
vagrant up
```

SSH into the VM:

```
vagrant ssh
```

Install Tesseract:

```bash
sudo apt-get install tesseract-ocr
```

Install the dependencies:

```bash
composer install
```

Visit the application in your browser:

```
http://homestead.app/
```

##Further Information

This application forms the basis of [this article on Sitepoint](http://www.sitepoint.com/ocr-in-php-read-text-from-images-with-tesseract/).
