make:
	# Create working directory if it doesn't exist
	mkdir -p ../chess
	# make sure that nothing is in the directory
	rm ../chess/* -i -rf
	# copy current content to the working directory
	cp -r * `readlink -f ../chess`
	# remove all svn files / folders
	cd ../chess

clean:
	rm -rf  *.class
