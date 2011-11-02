#define CURL_STATICLIB
#include <stdio.h>
#include <stdlib.h>
/* Install curl (sudo apt-get install curl libcurl3-dev) if this throws an error */
#include <curl/curl.h>
#include <curl/types.h>
#include <curl/easy.h>
#include <string.h>

size_t write_data(void *ptr, size_t size, size_t nmemb, FILE *stream) {
    size_t written;
    written = fwrite(ptr, size, nmemb, stream);
    return written;
}

char* read_data() {
    // thanks to Johannes Schaub - litb
    // http://stackoverflow.com/questions/410943/reading-a-text-file-into-an-array-in-c
    FILE *f = fopen("returnedval.txt", "rb");
    fseek(f, 0, SEEK_END);
    long pos = ftell(f);
    fseek(f, 0, SEEK_SET);

    char *bytes = malloc(pos);
    fread(bytes, pos, 1, f);
    fclose(f);

    return(bytes); // do some stuff with it
    free(bytes); // free allocated memory - where do I need this?
}


char * queryWebpage(char *url_query) {
    /* This function sends a request to the xhrframework of community-chess.com */

    CURL *curl;
    FILE *fp;
    CURLcode res;
    char *base_url = "http://community-chess.com/xhrframework.php";
    char *url;

    /* make space for the new string (should check the return value ...) */
    url = malloc(strlen(base_url)+strlen(url_query)+1); 
    strcpy(url, base_url); /* copy name into the new var */
    strcat(url, url_query); /* add the extension */

    char outfilename[FILENAME_MAX] = "returnedval.txt";
    curl = curl_easy_init();
    if (curl) {
        fp = fopen(outfilename,"wb");
        curl_easy_setopt(curl, CURLOPT_URL, url);

        // Add header if the cookie has been set
        curl_easy_setopt(curl, CURLOPT_HEADER, 0);

        curl_easy_setopt(curl, CURLOPT_WRITEFUNCTION, write_data);
        curl_easy_setopt(curl, CURLOPT_WRITEDATA, fp);

        curl_easy_setopt(curl, CURLOPT_TIMEOUT, 180);

        res = curl_easy_perform(curl);
        curl_easy_cleanup(curl);
        fclose(fp);
    }

    return read_data();
}

int main(void) {
	char * returnedval = queryWebpage("?action=login&username=abc&password=abc");
    printf("%s\n", returnedval);

    return 0;
}
