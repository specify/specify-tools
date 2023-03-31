# XML Inspector

- Walk though a series of XML files
- Compare their structure to a defined schema
- Log any differences
- Run validation
- Generate a report
- Also, produce a list of distinct values for each XML attribute

## Features

- XML schema definition supports `switch` statements to use different
  schema based on arbitrary criteria. Nested switches are supported too

## Installation

1. Clone this repository and open current directory (`./xml-inspector`)
2. Copy Specify 6 form definitions into `./source/` directory
3. Modify [./config.js](./config.js) as per comments in that file
4. Start any kind of web-server from current directory:

   Example:

   Start simple python server on port 80 and serve files from current
   directory

   ```bash
   sudo python3 -m http.server 80
   ```

5. Open web browser at a URL used by the web-server.

   In the above example it is [http://localhost:80/](http://localhost:80/)
6. Open browser console to see output from the tool

   The output will be two objects:

   - First one is the XML schema, but with a `values` set added to
     each attribute definition - it contains distinct list of values
     used for that attribute across all the files
   - Second one is all the warnings that occurred when validating XML
     against the schema

## Remarks

Tool is running in the browser rather than in Node.js because it relies on DOM
APIs for XML parsing.

Tool was used to scan all Specify 6 form definitions to discover a list of all
the attributes that Specify 6 uses, and how they should be handled by the
Specify 7 form editor.
