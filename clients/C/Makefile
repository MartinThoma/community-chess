CC=     gcc
LD=     ld
CFLAGS= -W -Wall -Werror
TARGET= myClient
SRC=    client.c
OBJ=    $(SRC:%.c=%.o)

$(TARGET): $(OBJ)
	$(CC) -lcurl -o $@ $(OBJ)

clean:
	rm -rf  $(TARGET) *.o returnedval.txt

distclean: clean
	rm -rf $(TARGET)
