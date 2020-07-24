PRAGMA foreign_keys = off;
BEGIN TRANSACTION;

CREATE TABLE cities (
    cityId   INTEGER PRIMARY KEY,
    cityName TEXT    NOT NULL
                     UNIQUE
);

CREATE TABLE places (
    placeId   INTEGER PRIMARY KEY,
    placeName TEXT    NOT NULL
                      UNIQUE,
    cityId    INTEGER NOT NULL,
    distance  REAL    NOT NULL,
    FOREIGN KEY (
        cityId
    )
    REFERENCES cities (cityId) 
);

CREATE TABLE travelers (
    travelerId INTEGER PRIMARY KEY,
    name       TEXT    NOT NULL
);

CREATE TABLE rates (
    travelerId INTEGER NOT NULL,
    placeId    INTEGER NOT NULL
                       REFERENCES places (placeId),
    rate       INTEGER NOT NULL,
    FOREIGN KEY (
        travelerId
    )
    REFERENCES travelers (travelerId),
    FOREIGN KEY (
        placeId
    )
    REFERENCES places (placeId),
    CHECK (rate >= 1 AND 
           rate <= 10) 
);

CREATE TABLE visits (
    travelerId INTEGER NOT NULL,
    cityId     INTEGER NOT NULL,
    FOREIGN KEY (
        travelerId
    )
    REFERENCES travelers (travelerId),
    FOREIGN KEY (
        cityId
    )
    REFERENCES cities (cityId) 
);

COMMIT TRANSACTION;
PRAGMA foreign_keys = on;
