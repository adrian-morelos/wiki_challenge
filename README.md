# Wiki challenge - Drupal 8 Module.
Wiki Challenge module implementation, which defines functionalities to search nodes of type 'Wikipedia Articles' that contains a given search parameter in its title, either searching by terms using the search box below or by providing a search term on the URL by following the route pattern: /wiki/[search_term].

Once the **'Wiki Challenge'** module is installed, a type of content called **'Wikipedia Article'** is automatically added and configured along with a view mode called **'Wikipedia Article Search Result'**.

After that **wiki challenge's** module is installed 30 nodes of type **'Wikipedia Article'** are automatically generated using the service `'wiki_challenge.generate.content'` implemented on this module(This logic can be found on the implementation of the hook: `'hook_modules_installed'` of the Wiki Challenge's module)

On the *Wiki search page* (/wiki), you can search nodes of type **'Wikipedia Articles'** that contains a given parameter in its title, either searching by terms using the search box below or by providing a search term on the URL by following the route pattern: `/wiki/[search_term]`.

Module Coding standard checked with [Coder Sniffer](https://www.drupal.org/project/coder) Version 8.2.12 - Coder is a library to review Drupal code.

Schreenshot:

![Wki challenge Search Page](screenshot/wiki-challenge.png)