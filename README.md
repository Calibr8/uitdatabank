# About

* Data sync with UiTdatabank.
* GET only.
* Configurable parameters.

http://documentatie.uitdatabank.be/content/search_api_3/latest/

Requirements:

* UiTdatabank API key required.

# TODO
* Remove comment dependency, once display settings work without.
* Correct UitdatabankConfiguration::API_PAGE_MAX_ITEMS when paging issues in API are fixed.
* Remove upper import limit of 10000 when paging issues in API are fixed, in UitdatabankJson.php.
* Find a way to update only present organizers and places, including those without an id.
* Find a way to cleanup obsolete events, organizers, places, media,...