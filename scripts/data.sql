BEGIN;

INSERT INTO product_category (id, name)
VALUES (1, 'Hevikitarat'), (2, 'Stratocasterit'), (3, 'Akkarit');

INSERT INTO product (id, name, description, image_url, price, product_category_id)
VALUES (1, 'Harley Benttonin halpis', 'Aina epävireessä', 'https://fast-images.static-thomann.de/pics/bdb/_15/155255/14305508_800.jpg', 299, 1),
(2, 'Schecterin hevikeppi', 'Tästä kitarasta löytyy kampi, jota on mukava veivata.', 'https://bdbo1.thomann.de/thumb/bdb3000/pics/bdbo/16978724.jpg', 299, 1),
(3, 'Fender Stratocaster', 'Legenda, joka ei esittelyjä kaipaa.', 'https://fast-images.static-thomann.de/pics/bdb/_50/500675/19282776_800.jpg', 3100, 2),
(4, 'Fender Red Strato', 'Punainen paholainen', 'https://fast-images.static-thomann.de/pics/bdb/_54/548695/19243393_800.jpg', 1299, 2),
(5, 'Harley Benton White Strat', 'Valkoinen ja halpa', 'https://fast-images.static-thomann.de/pics/bdb/_13/135304/19535195_800.jpg', 399, 2),
(6, 'Solar Guitars Cannibalismo', 'Väriä elämään', 'https://fast-images.static-thomann.de/pics/bdb/_53/538136/18011942_800.jpg', 1399, 2),
(7, 'Martin Johnny Cash Signature', 'Tähän luotti country-legendakin', 'https://fast-images.static-thomann.de/pics/bdb/_55/556129/18060617_800.jpg', 9999, 3),
(8, 'Martin Akkari', 'Timanttilaatua sinulle.', 'https://fast-images.static-thomann.de/pics/bdb/_59/595170/19870025_800.jpg', 5999, 3);

COMMIT;