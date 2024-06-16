1. GET|HEAD        api/accounts .................................................................................. accounts.index › AccountController@index
2. POST            api/accounts .................................................................................. accounts.store › AccountController@store
3. GET|HEAD        api/accounts/{account} .......................................................................... accounts.show › AccountController@show
4. PUT|PATCH       api/accounts/{account} ...................................................................... accounts.update › AccountController@update
5. DELETE          api/accounts/{account} .................................................................... accounts.destroy › AccountController@destroy
6. POST            api/login ......................................................................................................... AuthController@login
7. POST            api/logout ....................................................................................................... AuthController@logout
8. POST            api/register ................................................................................................... AuthController@register


Route list

1. Register user on api/register user, by sending params such as name, email, password, obtain JWT token and use it as bearer token for other routes;
2. Api/accounts get all registered accounts
3. api/accounts Register new account by sending params account_name, website_url, username, password, note
4. api/accounts/{account}, get single account by sending id of account on {account}
5. api/accounts/{account} Route for updating single account, by sending any or all params
6. api/accounts/{account} Delete account by sending id of account that you want to delete
7. api/logout destroy current active JWT
