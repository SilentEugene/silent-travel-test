# silent-travel-test

## GET

+ **?entity=traveler&id=1** - получение информации о путешественнике
+ **?entity=place&id=1** - получение информации о достопримечательности
+ **?entity=city&id=1** - получение информации о городе

+ **?entity=traveler&action=list** - получение списка путешественников
+ **?entity=place&action=list** - получение списка достопримечательностей
+ **?entity=city&action=list** - получение списка городов

## POST

+ Получение списка путешественников, посетивших город
  + entity = traveler
  + action = list
  + city = 1 - ID города
+ Получение списка достопримечательностей в городах
  + entity = place
  + action = list
  + cities = 1,3 - ID городов
+ Получение списка достопримечательностей, посещённых путешественником
  + entity = place
  + action = list
  + traveler = 1 - ID путешественника
+ Получение списка городов, посещённых путешественником
  + entity = city
  + action = list
  + traveler = 1 - ID путешественника
