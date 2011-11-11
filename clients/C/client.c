/**
 * This C client should provide some very basic actions for communication with the
 * Communit Chess server
 *
 * I've used snippets from the following web pages:
 *   http://rosettacode.org/wiki/Web_scraping#C
 *
 * Coding Style: KR style formatting
 *
 * @category Web_Services
 * @package  Community-chess
 * @author   Martin Thoma <info@martin-thoma.de>
 * @license  http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version  SVN: <svn_id>
 * @link     http://code.google.com/p/community-chess/
 */

#include <stdio.h>
#include <string.h>
#include <stdlib.h>
/* Install curl (sudo apt-get install curl libcurl3-dev) if this throws an error */
#include <curl/curl.h>
 
#define BUFSIZE 16384
 
size_t lr = 0;

struct gameInformation {
    char field[8][8];
    char playerColor;
    char currentPlayerColor;
    char *phpsessid;
};
 
size_t filterit(void *ptr, size_t size, size_t nmemb, void *stream)
{
    if ((lr + size * nmemb) > BUFSIZE)
        return BUFSIZE;
    memcpy(stream + lr, ptr, size * nmemb);
    lr += size * nmemb;
    return size * nmemb;
}

char *queryWebpage(char *url_query)
{
    /* This function sends a request to the xhrframework of community-chess.com */
    CURL *curlHandle;
    char buffer[BUFSIZE];

    char *base_url = "http://community-chess.com/xhrframework.php";
    char *url;

    /* make space for the new string (should check the return value ...) */
    url = malloc(strlen(base_url) + strlen(url_query) + 1); 
    strcpy(url, base_url);      /* copy name into the new var */
    strcat(url, url_query);     /* add the extension */

    curlHandle = curl_easy_init();
    curl_easy_setopt(curlHandle, CURLOPT_URL, url);
    curl_easy_setopt(curlHandle, CURLOPT_FOLLOWLOCATION, 1);
    curl_easy_setopt(curlHandle, CURLOPT_WRITEFUNCTION, filterit);
    curl_easy_setopt(curlHandle, CURLOPT_WRITEDATA, buffer);
    int success = curl_easy_perform(curlHandle);
    curl_easy_cleanup(curlHandle);

    if (success != 0) {
        printf("No success from curl_easy_perform.\n");
    }

    buffer[lr] = 0;
    char *returnval = malloc(sizeof(char) * 10);
    sprintf(returnval, "%s", &buffer[0]);
    return returnval;
}

int main()
{
    struct gameInformation myGameInformation;
    
    printf("Welcome to the C client for Community Chess\n");
    printf("Login ...\n");
    //char phpsessid[100];
    //strcpy(phpsessid, queryWebpage("?action=login&username=abc&password=abc") );

    // Login into account
	char *phpsessid = queryWebpage("?action=login&username=abc&password=abc");
    printf("Your current session id: %s\n", phpsessid);

    myGameInformation.phpsessid = phpsessid;

    printf("Gave information to struct.\n");

    char *gameList = queryWebpage("?action=listCurrentGames");
    printf("Game list:%s\n", gameList);
    printf("Done\n");
    return 0;
}
