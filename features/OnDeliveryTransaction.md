OnDeliveryTransactionController functions:
- createInvoice() creates an invoice for a set of borrowed books and enables the admin to return those books to the
library (used for task asking for on delivery payment possibility)
- createDomPdf() creates a pdf invoice and saves it to public/pdf
- singleBookInvoice() creates an invoice for a single book if the user wants to return only one book
- onDeliveryTransaction() stores on delivery transaction info in the database