# IPAPZ_Library_Management
Javni dio:
1. Vidi se popis knjiga u knjižnici i vidi se broj korisnika knjižnice, vidi se broj izdanih knjiga i broj knjiga koje su u knjižnici

Privatni dio:
1. User (djelatnik knjižnice) - može pregledavati i unositi knjige, žanr knjige i korisnike knjižnice
2. Admin - može dodavati nove usere, knjige i žanrove
3. User može kreirati novu posudbu, na posudbu može staviti više knjiga i definirati datum vraćanja knjiga
4. User može vratiti cijelu posudbu odjednom ili sa posudbe odabrati određene knjige i vratiti ih (ostale mogu ostati)

Poželjne funkcionalnosti:
1. User može mijenjati posudbu, mijenjati korisnike knjižnice i brisati ih i žanrove isto tako 
2. Admin može mijenjati usere, knjige i žanrove i brisati ih
3. Knjige se prikazuju 10 po stranici i učitavaju se putem ajaxa
4. Svakoj knjizi je moguće dodijeliti galeriju slika od kojih je jedna glavna i prikazuje se uz knjigu


Opcionalne funkcionalnosti:
1. Svakoj knjizi se na osnovu ISBN-a generira 2d barkod
2. Za svaku posudbu se kreira pdf
3. Export u Excel omogućuje ispis svih knjiga s ukupnim brojem dana u godini koliko su bili na posudbi (za danu godinu)
4. Svakoj knjizi se može dodijeliti pokemon
5. Svakom korisniku se po vraćanju knjige šalje izvještaj o vraćenim knjigama u pdfu.


phpcs src