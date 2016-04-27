main:
	echo "hello";
	
setup:
	chmod -R 777 userfiles
	
build: 
	gulp


.PHONY: main setup
