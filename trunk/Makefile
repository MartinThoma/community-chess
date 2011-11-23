make:
	# Create working directory if it doesn't exist
	mkdir -p ../chess
	# make sure that nothing is in the directory
	rm ../chess/* -i -rf
	# copy current content to the working directory
	cp -r * `readlink -f ../chess`
	# remove all svn files / folders
	find ../chess/ -name ".svn" -type d -print0 | xargs -0 rm -rf 

	# create the compressed css
	java -jar /var/www/closure/closure-stylesheets/build/closure-stylesheets.jar --output-file ../chess/styling/default-compiled.css ../chess/styling/default.gss

	# remove unnecessary files / folders
	rm -rf ../chess/documentation
	rm -rf ../chess/design
	rm ../chess/Makefile
	rm ../chess/*README
	rm ../chess/styling/default.gss
	rm -rf  *.class
	rm -rf  *.o

clean:
	rm -rf  *.class
	rm -rf  *.o
