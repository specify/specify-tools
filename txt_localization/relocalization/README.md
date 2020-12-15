# relocalization/
Scripts that were used for the second wave of localization for the `UI` componentes

Workflow:
 * Convert existing `.properties` files to `.tsv` files using `properties_to_tsv.py`
 * Convert `.tsv` files to `.xlsx` using external tools
 * Edit `.xlsx` files to fix localization errors
 * Convert localized `.xlsx` back to `.tsv` using `xlsx_to_tsv.py`

## properties_to_tsv.py
Converts .properties files to .tsv files

## xlsx_to_tsv.py
Converts .xlsx files to .tsv
