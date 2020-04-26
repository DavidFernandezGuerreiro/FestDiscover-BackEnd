# API Rest to get data from the festdiscover database

>(Note: There are more endpoints create, but they are used within other endpoints.)

#### Parameters
- task
    - It's the endpoint we want to request

## Endpoints

### Festivals

#### GetAllFestivals
- This endpoint collect all the festivals

http://localhost/FestDiscover-BackEnd/festivals?task=get_all_festivals
___
#### GetFestivalById
- This endpoint collect one festival by idFestival

http://localhost/FestDiscover-BackEnd/festivals?task=get_festival&id_festival=1

##### Required parameters
    - id_festival (number)
___
#### GetFestivalsByFilter
- This endpoint collects the festivals filtering by country, musical gender, min and max price

http://localhost/FestDiscover-BackEnd/festivals?task=get_festivals_by_filter

##### Required parameters (raw)
    {
        "id_gender": 2,
        "country": "Portugal",
        "min_price": 50,
        "max_price": 100
    }
___
#### CreateFestival
- This endpoint create a festival

http://localhost/FestDiscover-BackEnd/festivals?task=create_festival

##### Required parameters (raw)
    {
        "idProfile": 92,
        "name": "Nombre Festival",
        "description": "Descripción del festival.",
        "country": "España",
        "location": "Ubicación del festival",
        "initDate": "2020-07-14",
        "endDate": "2020-07-16",
        "linkTickets": "www.link-tickets.com",
        "price": 40.50,
        "musicGenders": [1, 2]
    }

##
### Users

#### GetAllUsers
- This endpoint collect all the users

http://localhost/FestDiscover-BackEnd/users?task=get_all_users
___
#### GetUserById
- This endpoint collect one user by idUser

http://localhost/FestDiscover-BackEnd/users?task=get_user&id_user=1

##### Required parameters
    - id_user (number)
___
#### CreateUser
- This endpoint create a user

http://localhost/FestDiscover-BackEnd/users?task=create_user

##### Required parameters (raw)
    {
        "name": "promotor",
        "email": "promotor@daw.com",
        "password": "abc123.",
        "nameProfile": "promotor daw",
        "idRole": 3
    }
___
#### UserLogin
- This endpoint allows a user to login

http://localhost/FestDiscover-BackEnd/users?task=user_login

##### Required parameters (raw)
    {
        "nameProfile": "profile name",
        "password": "abc123."
    }
___
#### UpdateUser
- This endpoint allows you to update a user

http://localhost/FestDiscover-BackEnd/users?task=update_user

##### Required parameters (raw)
    {
        "idUserUpdate": 97,
        "name": "Name Update",
        "password": "abc123."
    }
___
#### DeleteUser
- This endpoint allows you to delete an entire user.
    - If it is a user with the role of promoter, it deletes the profile and the festivals created.
    - If you are a user with the role user, delete the profile, musical tastes and favorite festivals.

http://localhost/FestDiscover-BackEnd/users?task=delete_user

##### Required parameters (raw)
    {
        "id": 91
    }

##
### Profiles

#### GetAllProfiles
- This endpoint collect all the profiles

http://localhost/FestDiscover-BackEnd/profiles?task=get_all_profiles
___
#### GetProfileById
- This endpoint collect one profile by idProfile

http://localhost/FestDiscover-BackEnd/profiles?task=get_profile&id_profile=1

##### Required parameters
    - id_profile (number)
___
#### UpdateProfile
- This endpoint allows you to update a profile

http://localhost/FestDiscover-BackEnd/profiles?task=update_profile

##### Required parameters (raw)
    {
        "id": 97,
        "name": "Pedro",
        "city": "Nigran",
        "province": "Pontevedra",
        "country": "España",
        "numberPhone": "655444111",
        "dateBirth": "99-07-14",
        "musicGenders": [1, 2]
    }
___
#### AddFavoriteFestival
- This endpoint adds a festival to favorites

http://localhost/FestDiscover-BackEnd/profiles?task=add_favorite_festival

##### Required parameters (raw)
    {
        "idProfile": 97,
        "idFestival": 1
    }
___
#### DeleteFavoriteFestival
- This endpoint eliminates a favorite festival

http://localhost/FestDiscover-BackEnd/profiles?task=delete_favorite_festival

##### Required parameters (raw)
    {
        "idProfile": 97,
        "idFestival": 1
    }

##
### MusicGenders

#### GetAllMusicGenders
- This endpoint collect all the music genders

http://localhost/FestDiscover-BackEnd/musicGenders?task=get_all_music_genders
___
#### GetMusicGenderById
- This endpoint collect one music gender by idGender

http://localhost/FestDiscover-BackEnd/musicGenders?task=get_music_gender&id_gender=1

##### Required parameters
    - id_gender (number)
___
#### GetFestivalsByIdGender
- This endpoint collects the festivals with a particular musical gender

http://localhost/FestDiscover-BackEnd/musicGenders?task=get_festival_by_gender

##### Required parameters (raw)
    {
        "id_gender": 1
    }
___
#### GetGenderByIdFestival
- This endpoint collects the musical genders of a festival

http://localhost/FestDiscover-BackEnd/musicGenders?task=get_gender_by_festival&id_festival=1

##### Required parameters
    - id_festival (number)

##
### Images

#### UploadImages
- This endpoint allows you to upload images

http://localhost/FestDiscover-BackEnd/images?task=upload_images

##### Required parameters (form-data)
    - upload_image (file png or jpeg)
    - idProfile (number)
___