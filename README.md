# SymfonyId Admin Bundle [SIAB]#

<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>

<img src="AdminBundle.png" alt="AdminBundle can speed up your development process" title="SymfonyIdAdminBundle" align="center" />

#### Key Features####

+ Admin Generator

+ ORM and ODM both support

+ User Management

+ CRUD Generator

+ Menu Generator

+ Bulk Insert

+ Bulk Delete

+ Auto Complete

+ Date(time) Picker

+ Soft Deletable

+ Timestampable

+ Filter and Sorting

+ Easy To Custom

#### Installation####

[How to install](Resources/doc/en/installation.md)

#### Contributors####
 
+ [Muhammad Surya Ihsanuddin](https://github.com/ihsanudin)

+ [Kontributor](https://github.com/SymfonyId/SymfonyIdAdminBundle/graphs/contributors)

#### LICENSE####
[LICENSE](LICENSE)
