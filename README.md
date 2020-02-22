# Joiner
Simple console script for make long file from several files.
This can help you in work with html/css or other long text documents.

## How to use
Just insert `@join:somefolder/filename.html;` if you need include file with name `filename.html` from folder `somefolder`
If you need repeated any file part 5 times, try:

    @repeat:5;
    Lorem ipsum dolor.
    @end;
    
and result 

    Lorem ipsum dolor.
    Lorem ipsum dolor.
    Lorem ipsum dolor.
    Lorem ipsum dolor.
    Lorem ipsum dolor.
Also you can write `@repeat:7; @join:filename.html; @end;`

To run the script **joiner**, you need to open a terminal and write a line like
`php joiner.php myfolder/input-file.html anotherfolder/output-file.html` and you can added word `watch` if needs compile your input file in live

[Release page](https://github.com/eugene-sukhodolskiy/joiner/tags)
