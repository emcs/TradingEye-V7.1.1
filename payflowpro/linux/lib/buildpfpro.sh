#!/bin/sh
# This script will build the Payflow Pro client: pfpro
# The compiler is the gnu compiler (gcc); for native compiler,
# see your OS manual (typically cc)

#This build requires the pfpro.c source file in the ../bin dir
#The header file pfpro.h is in the current dir
#The Payflow Pro library file libpfpro is in the current dir

TCINC=. 
TCLIB=.
TCFLAGS=" -DUSE_SSLEAY -xc -fPIC -DLINUX_OS -DLINUX_SEED   -D_REENTRANT -pthread -DPTHREADS"

echo "************ Building  pfpro... **********************"
gcc -v -c $TCFLAGS -I$TCINC ../bin/pfpro.c -o pfpro.o
gcc -v -o pfpro pfpro.o    -lpfpro   -L$TCLIB $TCFLAGS  
