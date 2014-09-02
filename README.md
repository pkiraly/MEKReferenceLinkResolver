= A projektről =
A projekt hátteréről és céljáról lásd: http://kirunews.blog.hu/2010/12/16/mek_referensz_link_feloldo

= Technikai leírás =
A linkfeloldó két fajta fájlt kezel: könyv-szintű és oldal szintű CSV fájlokat.
A CSV feloldása: comma separated values, egy egyszerű szövegfájl, 
amiben egy sor megfelel egy adattábla rekordjának, és az egyes mezőket
valamilyen elválasztójel, a mi esetünkben pontosvessző választja el. Az 
itt szereplő össze fájlban két mező van.

A könyv-szintű megfeleltetésnél egy-egy referensz mű (RMK, RMNy) 
vagy szabvány (ISBN) azonosítóját feleltetjük meg egy MEK-ben 
szereplő művel. Minden ilyen fájlból egy-egy darab van, ami tartalmazza
az adott megfeleltetés összes elemét, tehát például egyetlen rmk.csv, 
amiben benne van az összes MEK által szolgáltatott RMK tétel.

Az oldal-szintű azonosítás során a digitalizált könyv eredeti oldalszámait
feleltetjük meg a MEK-ben levő digitális másolat oldal-szintű URL-jeivel.
Itt tehát a külső referencia elveszti jelentősségét.

Jelenleg a következő megfeleltetések vannak:

| RMNy | RMK1 | MEK |
| --- | --- | --- |
|  17 |   7 | 12200/12273 |
|  80 | 322 | 12200/12278 |
| 109 |  33 | 12200/12294 |
| 303 | 338 | 11900/11991 |
| 327 |  94 | 11900/11957 |
| 348 | 109 | 12000/12046 |
| 350 | 111 | 12000/12050 |
| 353 | 332 | 08800/08838 |
| 359 | 117 | 12000/12036 |
| 385 | ﻿128﻿ | 12200/12277 |
| 386 | ﻿131﻿ | 12200/12272 |
| 437 | 154 | 12000/12045 |
| 438 | 155 | 12000/12030 |
| 439 | 156 | 12000/12023 |
| 441 | 159 | 12000/12035 |
| 458 | ﻿165﻿ | 12000/12022 |
| 460 | 166 | 12000/12031 |
| 461 | ﻿343﻿ | 12200/12282 |
| 468 | 174 | 12000/12027 |
| 492 | 181 | 12000/12039 |
| 493 | 182 | 12000/12020 |
| 510 | 193 | 12000/12019 |
| 531 | 210 | 12100/12124 |
| 540 | 208 | 03600/03614 |
| 858 | ﻿311﻿ | 12200/12281 |
| 869 | ﻿315﻿ | 12200/12295 |
| 956 | 362 | 12100/12140 |
| 957 | ﻿363﻿ | 12100/12139 |

# Könyv-szintű fájlok

## rmk.csv
az RMK számok és MEK azonosítók megfeleltetése. Adatmezők:

1. RMK azonosító. A formátum: [kötet]/[4 karakteres RMK szám][opcionális alfabetikus kiegészítő].
Ha az alap RMK szám nem 4 karakteres, akkor  balról 0-val kell feltölteni, például 94 helyett 0094-et kell írni.
2. [MEK könyvtárnév]/[MEK azonosító]

például:
<pre>
  1/0332;08800/08838
  1/0094;11900/11957
</pre>
ez azt jelenti, hogy 
* az RMK I 332. megfelel a mek.oszk.hu/08800/08838 könyvnek
* az RMK I 94. megfelel a mek.oszk.hu/11900/11957 könyvnek

## rmny.csv
RMNy számok és MEK azonosítók megfeleltetése. Adatmezők:

1. RMNy azonosító. A formátum: [4 karakteres RMNy szám][opcionális alfabetikus kiegészítő]
Ha az alap RMNy szám nem 4 karakteres, akkor balról 0-val kell feltölteni,
például 94 helyett 0094-et kell írni.
2. [MEK könyvtárnév]/[MEK azonosító]

például:
<pre>
  353;08800/08838
  0327;11900/11957
</pre>
ez azt jelenti, hogy
* az RMNy 353. megfelel a mek.oszk.hu/08800/08838 könyvnek
* az RMNy 327. megfelel a mek.oszk.hu/11900/11957 könyvnek

# Oldal-szintű fájlok

MEK tételenként egy-egy ilyen fájl lehetséges, mely tartalmazhatja az
összes oldalt, vagy csak kiválasztott, valamilyen szempontból fontosnak
tartott oldalakat.

A fájl neve mindig ezt a formát követi:
<pre>
  [MEK ID].csv
</pre>
ahol a [MEK ID] helyett egy konkrét MEK azonosító szerepel, például 08838.csv

A fájl célja MEK 08838-as könyv digitalizált oldalai URL-jeinek és a 
nyomtatott könyv tényleges oldalszámozásnak a megfeleltetése. 
Adatelemek:

1. a nyomtatott mű oldalszáma a következő formában: 
[kötet]/[4 karakteres oldalszám][alphabetikus kiegészítő] ahol a kötet és az
aphabetikus kiegészítő (például A, B, R (=recto), V (=verso) és így tovább)
opcionális elem.
2. a digitális oldal URL-je

például:
<pre>
  1/0003A;hu_b1_rmk-1-113a_012
</pre>
ez azt jelenti, hogy az első kötet 0003A oldala megfelel az mek.oszk.hu/08800/08838-en belül a hu_b1_rmk-1-113a_012.html oldalnak.
