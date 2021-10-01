# Geography Tree to TSV

Convert Specify's geography tree to a TSV file (a table).

## Steps

1. Run this query on the database, and save the output to a `.tsv` file:
    ```sql
    SELECT
      `GeographyID`,
      `Name`,
      `ParentID`
    FROM `geography` ORDER BY `RankID`;
   ```
2. Open `convert.js` and specify the destination to the `sourceFile` (generated
   in the previous step) and `resultFile` for the output of the conversion.
3. Run the script (assuming Node.js is installed):
   ```zsh
   node convert.js
   ```
4. Resulting TSV file is now saved in the `resultFile` location, and can be
   manually converted to XLSX if needed.